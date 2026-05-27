do $$
begin
    CALL register_user('gheal', 'alexandru.gheorghies@gmail.com','gheal');
    CALL register_user('gruia', 'poparobert2012@gmail.com','gruia');
    INSERT INTO inventories(name, description, owner_id) VALUES('Inventar 1', 'Primul inventar', 1);
    INSERT INTO inventories(name, description, owner_id) VALUES('Inventar 2', 'Al doilea inventar', 2);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(1, 1, 15);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(2, 2, 15);
    INSERT INTO fonduri(amount, currency_code, inventory_id) VALUES(1000, 'USD', 1);
end; $$ language plpgsql;