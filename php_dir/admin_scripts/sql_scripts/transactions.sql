-- PL/pgSQL helper functions for scheduled transactions
-- Functions:
--  - check_transaction_applicable(tx_id) RETURNS BOOLEAN
--  - apply_transaction(tx_id) -> applies the transaction (modifies resource quantity),
--      advances start_timestamp by frequency and deletes if past end_timestamp,
--      raises an error if not applicable
--  - skip_transaction(tx_id) -> advances start_timestamp by frequency, deletes if past end_timestamp

CREATE OR REPLACE FUNCTION check_transaction_applicable(p_tx_id BIGINT)
RETURNS BOOLEAN LANGUAGE plpgsql AS $$
DECLARE
	t RECORD;
	cur_qty DOUBLE PRECISION;
	new_qty DOUBLE PRECISION;
BEGIN
	SELECT * INTO t FROM transactions WHERE id = p_tx_id;
	IF NOT FOUND THEN
		RAISE EXCEPTION 'Transaction % not found', p_tx_id;
	END IF;

	SELECT quantity INTO cur_qty FROM resources WHERE id = t.resource_id;
	IF NOT FOUND THEN
		RAISE EXCEPTION 'Resource % referenced by transaction % not found', t.resource_id, p_tx_id;
	END IF;

	new_qty := cur_qty + t.quantity_change;
	RETURN new_qty >= 0;
END;
$$;

CREATE OR REPLACE FUNCTION apply_transaction(p_tx_id BIGINT)
RETURNS VOID LANGUAGE plpgsql AS $$
DECLARE
	t RECORD;
	new_start TIMESTAMPTZ;
	applicable BOOLEAN;
BEGIN
	-- lock the transaction row to avoid races
	SELECT * INTO t FROM transactions WHERE id = p_tx_id FOR UPDATE;
	IF NOT FOUND THEN
		RAISE EXCEPTION 'Transaction % not found', p_tx_id;
	END IF;

	applicable := check_transaction_applicable(p_tx_id);
	IF NOT applicable THEN
		RAISE EXCEPTION 'Transaction % is not applicable (insufficient resource quantity)', p_tx_id;
	END IF;

	-- apply quantity change
	UPDATE resources
	SET quantity = quantity + t.quantity_change
	WHERE id = t.resource_id;

	-- if one-time (no frequency) remove the transaction
	IF t.frequency IS NULL THEN
		DELETE FROM transactions WHERE id = p_tx_id;
		RETURN;
	END IF;

	-- otherwise advance start_timestamp by frequency
	new_start := t.start_timestamp + t.frequency;

	IF t.end_timestamp IS NOT NULL AND new_start > t.end_timestamp THEN
		-- next occurrence would be past end -> remove
		DELETE FROM transactions WHERE id = p_tx_id;
	ELSE
		UPDATE transactions SET start_timestamp = new_start WHERE id = p_tx_id;
	END IF;
END;
$$;

CREATE OR REPLACE FUNCTION skip_transaction(p_tx_id BIGINT)
RETURNS VOID LANGUAGE plpgsql AS $$
DECLARE
	t RECORD;
	new_start TIMESTAMPTZ;
BEGIN
	SELECT * INTO t FROM transactions WHERE id = p_tx_id FOR UPDATE;
	IF NOT FOUND THEN
		RAISE EXCEPTION 'Transaction % not found', p_tx_id;
	END IF;

	-- if one-time, removing it is equivalent to skipping
	IF t.frequency IS NULL THEN
		DELETE FROM transactions WHERE id = p_tx_id;
		RETURN;
	END IF;

	new_start := t.start_timestamp + t.frequency;
	IF t.end_timestamp IS NOT NULL AND new_start > t.end_timestamp THEN
		DELETE FROM transactions WHERE id = p_tx_id;
	ELSE
		UPDATE transactions SET start_timestamp = new_start WHERE id = p_tx_id;
	END IF;
END;
$$;

