do $$
begin
    CALL register_user('gheal', 'alexandru.gheorghies@gmail.com','gheal');
    CALL register_user('gruia', 'poparobert2012@gmail.com','gruia');
    INSERT INTO inventories(name, description, owner_id) VALUES('Inventar 1', 'Primul inventar', 1);
    INSERT INTO inventories(name, description, owner_id) VALUES('Inventar 2', 'Al doilea inventar', 2);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(1, 1, 15);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(1, 2, 15);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(2, 1, 15);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(2, 2, 15);
    INSERT INTO fonduri(amount, currency_code, inventory_id) VALUES(1000, 'USD', 1);
    INSERT INTO fonduri(amount, currency_code, inventory_id) VALUES(10000, 'RON', 1);
    INSERT INTO resources(name, description, quantity, unit, inventory_id) VALUES('Resursa 1', 'Prima resursa', 10, 'bucati', 1);
    INSERT INTO resources(name, description, quantity, unit, inventory_id) VALUES('Resursa 2', 'A doua resursa', 20, 'bucati', 1);
    INSERT INTO tags(name, bgcolor, fgcolor, inventory_id) VALUES('Dedeman', '#DDDDFF', '#000000', 1);
    INSERT INTO tags(name, bgcolor, fgcolor, inventory_id) VALUES('Evil Dedeman', '#2222FF', '#FFFFFF', 1);
end; $$ language plpgsql;