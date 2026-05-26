create or replace function hash_password(p_password varchar(64)) returns user_table.password_hash%type as $$
begin
    return crypt(p_password, gen_salt('md5'));
end; $$ language plpgsql;

create or replace procedure register_user(p_username user_table.username%type, p_email user_table.email%type, p_password varchar(64)) as $$
begin
    insert into user_table(username, email, password_hash) values(p_username, p_email, hash_password(p_password));
end; $$ language plpgsql;

create or replace function authenticate_user(p_username user_table.username%type, p_password varchar(64)) returns boolean as $$ 
declare
    retval int;
begin
    select count(*) into retval from user_table where username = p_username and password_hash = crypt(p_password, password_hash); 
    return retval > 0;
end; $$ language plpgsql; 