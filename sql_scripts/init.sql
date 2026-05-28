DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS has_tag;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS fonduri;
DROP TABLE IF EXISTS currencies;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS user_inventory_permission;
DROP TABLE IF EXISTS inventories;
DROP TABLE IF EXISTS password_recovery_codes;
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id                    BIGSERIAL PRIMARY KEY,
    username              VARCHAR(255) UNIQUE NOT NULL,
    email                 VARCHAR(255) UNIQUE NOT NULL, 
    password_hash         VARCHAR(64) NOT NULL
);

CREATE TABLE password_recovery_codes (
    id                    BIGSERIAL PRIMARY KEY,
    user_id               BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    code                  VARCHAR(255) NOT NULL,
    expires_at            TIMESTAMP NOT NULL
);


CREATE TABLE inventories (
    id                     BIGSERIAL PRIMARY KEY,
    name                   VARCHAR(255) NOT NULL,
    description            TEXT,
    owner_id               BIGINT REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(name, owner_id)
);

CREATE TABLE user_inventory_permission (
    id                     BIGSERIAL PRIMARY KEY,
    user_id                BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    inventory_id           BIGINT NOT NULL REFERENCES inventories(id) ON DELETE CASCADE,
    permissions            INT NOT NULL DEFAULT 0, -- delete update edit read
    CHECK ((permissions & 1) > 0 OR (permissions & 14) = 0),
    UNIQUE(user_id, inventory_id) -- un user poate avea doar un set de permisiuni pe un inventory
);

CREATE TABLE resources (
    id                     BIGSERIAL PRIMARY KEY,
    name                   VARCHAR(255) NOT NULL,
    description            TEXT,
    quantity               DOUBLE PRECISION NOT NULL CHECK (quantity >= 0),
    unit                   VARCHAR(50) NOT NULL, -- ce inseamna "o unitate" in contextul acestei resurse
    inventory_id           BIGINT NOT NULL REFERENCES inventories(id) ON DELETE CASCADE,
    UNIQUE(name, inventory_id) -- toate resursele din acelasi inventory au nume diferite
);

CREATE TABLE tags (
    id                     BIGSERIAL PRIMARY KEY,
    name                   VARCHAR(255) NOT NULL,
    bgcolor VARCHAR(7)    NOT NULL DEFAULT '#DDDDFF',
    fgcolor VARCHAR(7)    NOT NULL DEFAULT '#000000',
    inventory_id           BIGINT NOT NULL REFERENCES inventories(id) ON DELETE CASCADE,
    UNIQUE(name, inventory_id)
);


CREATE TABLE currencies (
    code                   VARCHAR(3) PRIMARY KEY -- ISO 4217 currency code
);

CREATE TABLE fonduri (
    id                     BIGSERIAL PRIMARY KEY,
    amount                 DOUBLE PRECISION NOT NULL,
    currency_code          VARCHAR(3) NOT NULL REFERENCES currencies(code),
    inventory_id           BIGINT NOT NULL REFERENCES inventories(id) ON DELETE CASCADE,
    name                   VARCHAR(255),
    description            TEXT
    unique(inventory_id, name)
);

CREATE TABLE transactions (
    id                     BIGSERIAL PRIMARY KEY,
    resource_id            BIGINT NOT NULL REFERENCES resources(id) ON DELETE CASCADE,
    currency_code          VARCHAR(3) REFERENCES currencies(code),
    quantity_change        DOUBLE PRECISION NOT NULL, -- poate fi pozitiv sau negativ
    total_price_change     DOUBLE PRECISION, -- poate fi pozitiv sau negativ, NULL daca tranzactia nu implica schimb valutar
    start_timestamp        TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    end_timestamp          TIMESTAMPTZ, -- NULL daca tranzactia e one time
    frequency              INTERVAL, -- NULL daca tranzactia e one time
    description            TEXT
);