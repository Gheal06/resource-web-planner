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
    owner_id               BIGINT REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE user_inventory_permission (
    id                     BIGSERIAL PRIMARY KEY,
    user_id                BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    inventory_id           BIGINT NOT NULL REFERENCES inventories(id) ON DELETE CASCADE,
    permissions            INT NOT NULL DEFAULT 0, -- insert update delete read
    CHECK ((permissions & 1) > 0 OR (permissions & 14) = 0)
);

CREATE TABLE resources (
    id                     BIGSERIAL PRIMARY KEY,
    name                   VARCHAR(255) NOT NULL,
    description            TEXT,
    quantity               DOUBLE PRECISION NOT NULL,
    unit                   VARCHAR(50) NOT NULL, -- ce inseamna "o unitate" in contextul acestei resurse
    inventory_id           BIGINT NOT NULL REFERENCES inventories(id) ON DELETE CASCADE
);

CREATE TABLE currencies (
    id                     BIGSERIAL PRIMARY KEY,
    code                   VARCHAR(3) UNIQUE NOT NULL -- ISO 4217 currency code
);