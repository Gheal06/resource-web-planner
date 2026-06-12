create extension if not exists pgcrypto;
create or replace function hash_password(p_password varchar(64)) returns users.password_hash%type as $$
begin
    return crypt(p_password, gen_salt('md5'));
end; $$ language plpgsql;

create or replace procedure register_user(p_username users.username%type, p_email users.email%type, p_password varchar(64)) as $$
begin
    insert into users(username, email, password_hash) values(p_username, p_email, hash_password(p_password));
end; $$ language plpgsql;

create or replace function authenticate_user(p_username users.username%type, p_password varchar(64)) returns boolean as $$ 
declare
    retval int;
begin
    select count(*) into retval from users where username = p_username and password_hash = crypt(p_password, password_hash); 
    return retval > 0;
end; $$ language plpgsql; 

create or replace procedure change_user_password(p_username users.username%type, p_new_password varchar(64)) as $$
begin
    update users set password_hash = hash_password(p_new_password) where username = p_username;
end; $$ language plpgsql;