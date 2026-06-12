do $$
begin
    CALL register_user('gheal', 'alexandru.gheorghies@gmail.com','gheal');
    CALL register_user('gruia', 'poparobert2012@gmail.com','gruia');
    INSERT INTO notifications(user_id, title, message) VALUES(1, 'Test Account', 'Your account has been artificially inserted into the DB as a test account');
    INSERT INTO notifications(user_id, title, message) VALUES(2, 'Test Account', 'Your account has been artificially inserted into the DB as a test account');
    INSERT INTO inventories(name, description, owner_id) VALUES('Inventar 1', 'Primul inventar', 1);
    INSERT INTO inventories(name, description, owner_id) VALUES('Inventar 2', 'Al doilea inventar', 2);
    INSERT INTO admins(user_id) VALUES(1);
    INSERT INTO admins(user_id) VALUES(2);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(1, 1, 15);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(1, 2, 15);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(2, 1, 15);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(2, 2, 15);
    INSERT INTO fonduri(name, amount, currency_code, inventory_id, threshold_amount) VALUES('Bani de la matusa', 1000, 'USD', 1, 500);
    INSERT INTO fonduri(name, amount, currency_code, inventory_id, threshold_amount) VALUES('Bani de la bunicu', 10000, 'RON', 1, 5000);
    INSERT INTO resources(name, description, quantity, unit, inventory_id, threshold_quantity) VALUES('Resursa 1', 'Prima resursa', 10, 'bucati', 1, 5);
    INSERT INTO resources(name, description, quantity, unit, inventory_id, threshold_quantity) VALUES('Resursa 2', 'A doua resursa', 20, 'bucati', 1, 10);
    INSERT INTO tags(name, bgcolor, fgcolor, inventory_id) VALUES('Dedeman', '#DDDDFF', '#000000', 1);
    INSERT INTO tags(name, bgcolor, fgcolor, inventory_id) VALUES('Evil Dedeman', '#2222FF', '#FFFFFF', 1);
    INSERT INTO has_tag VALUES (1, 1);
    INSERT INTO has_tag VALUES (1, 2);
end; $$ language plpgsql;