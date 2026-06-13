do $$
begin
    CALL register_user('gheal', 'alexandru.gheorghies@gmail.com','gheal');
    CALL register_user('gruia', 'poparobert2012@gmail.com','gruia');
    INSERT INTO notifications(user_id, title, message) VALUES(1, 'Test Account', 'Your account has been artificially inserted into the DB as a test account');
    INSERT INTO notifications(user_id, title, message) VALUES(2, 'Test Account', 'Your account has been artificially inserted into the DB as a test account');
    INSERT INTO inventories(name, description, owner_id) VALUES('Home', 'Home Inventory', 1);
    INSERT INTO inventories(name, description, owner_id) VALUES('Work', 'Work Inventory', 2);
    INSERT INTO admins(user_id) VALUES(1);
    INSERT INTO admins(user_id) VALUES(2);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(1, 1, 15);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(1, 2, 15);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(2, 1, 15);
    INSERT INTO user_inventory_permission(user_id, inventory_id, permissions) VALUES(2, 2, 15);
    INSERT INTO fonduri(name, amount, currency_code, inventory_id, threshold_amount) VALUES('Revenue (EUR)', 1000, 'EUR', 2, 500);
    INSERT INTO fonduri(name, amount, currency_code, inventory_id, threshold_amount) VALUES('Revenue (RON)', 10000, 'RON', 2, 5000);
    INSERT INTO resources(name, description, quantity, unit, inventory_id, threshold_quantity) VALUES('Milk', 'Milk', 4, 'L', 1, 1);
    INSERT INTO resources(name, description, quantity, unit, inventory_id, threshold_quantity) VALUES('00 Flour', 'Flour for pizza', 5, 'kg', 1, 1);
    INSERT INTO resources(name, description, quantity, unit, inventory_id, threshold_quantity) VALUES('All-Purpose Flour', 'Regular flour', 5, 'kg', 1, 1);
    INSERT INTO resources(name, description, quantity, unit, inventory_id, threshold_quantity) VALUES('Eggs', 'Regular eggs', 20, '', 1, 3);
    INSERT INTO resources(name, description, quantity, unit, inventory_id, threshold_quantity) VALUES('Screws', 'Regular screws', 30, 'pcs', 1, 10);
    INSERT INTO fonduri(name, amount, currency_code, inventory_id, threshold_amount) VALUES('Balance (RON)', 9990, 'RON', 1, 500);
    INSERT INTO fonduri(name, amount, currency_code, inventory_id, threshold_amount) VALUES('Balance (EUR)', 110, 'EUR', 1, 0);
    INSERT INTO fonduri(name, amount, currency_code, inventory_id, threshold_amount) VALUES('Balance (USD)', 100, 'USD', 1, 0);
    INSERT INTO tags(name, bgcolor, fgcolor, inventory_id) VALUES('Food', '#ffd502', '#000000', 1);
    INSERT INTO tags(name, bgcolor, fgcolor, inventory_id) VALUES('Flour', '#b9b9b9', '#000000', 1);
    INSERT INTO tags(name, bgcolor, fgcolor, inventory_id) VALUES('Crafts', '#000000', '#ffffff', 1);
    INSERT INTO has_tag VALUES (1, 1);
    INSERT INTO has_tag VALUES (2, 1);
    INSERT INTO has_tag VALUES (2, 2);
    INSERT INTO has_tag VALUES (3, 1);
    INSERT INTO has_tag VALUES (3, 2);
    INSERT INTO has_tag VALUES (4, 1);
    INSERT INTO has_tag VALUES (5, 3);
    INSERT INTO resource_transaction_history (
        id,resource_id,resource_name,inventory_id,operation_type,quantity_change,old_quantity,new_quantity,
        description,created_at,created_by
    ) values (
        1,1,'Milk',1,'Add',1,3,4,
        'Bought Milk',NOW(),1
    );
    INSERT INTO fonduri_transaction_history (
        id,fonduri_id,fonduri_name,currency_code,inventory_id,operation_type,amount_change,old_amount,new_amount,
        description,created_at,created_by
    ) values (
        1,1,'Balance (RON)','RON',1,'Add',5000,5000,10000,
        'Salary',NOW(),1
    );
    INSERT INTO fonduri_transaction_history (
        id,fonduri_id,fonduri_name,currency_code,inventory_id,operation_type,amount_change,old_amount,new_amount,
        description,created_at,created_by
    ) values (
        2,1,'Balance (RON)','RON',1,'Subtract',10,10000,9990,
        'Bought Milk',NOW(),1
    );
end; $$ language plpgsql;