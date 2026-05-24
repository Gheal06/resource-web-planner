DROP TABLE IF EXISTS resources_table;
DROP TABLE IF EXISTS user_table_permission;
DROP TABLE IF EXISTS inventory_table;
DROP TABLE IF EXISTS user_table;
DROP TABLE IF EXISTS users;
CREATE TABLE user_table (
    id                    BIGSERIAL PRIMARY KEY,
    username              VARCHAR(255) UNIQUE NOT NULL,
    email                 VARCHAR(255) UNIQUE NOT NULL, 
    password_hash         VARCHAR(255) NOT NULL
);
COMMIT;

CREATE TABLE inventory_table (
    id                     BIGSERIAL PRIMARY KEY,
    name                   VARCHAR(255) NOT NULL,
    description            TEXT,
    owner_id               BIGINT NOT NULL REFERENCES user_table(id) ON DELETE CASCADE
);

CREATE TABLE user_table_permission (
    id                     BIGSERIAL PRIMARY KEY,
    user_id                BIGINT NOT NULL REFERENCES user_table(id) ON DELETE CASCADE,
    inventory_id           BIGINT NOT NULL REFERENCES inventory_table(id) ON DELETE CASCADE,
    can_insert             BOOLEAN NOT NULL DEFAULT FALSE,
    can_update             BOOLEAN NOT NULL DEFAULT FALSE,
    can_delete             BOOLEAN NOT NULL DEFAULT FALSE,
    can_read               BOOLEAN NOT NULL DEFAULT FALSE,
    CHECK (can_read OR NOT (can_insert OR can_update OR can_delete))
);

CREATE TABLE resources_table (
    id                     BIGSERIAL PRIMARY KEY,
    name                   VARCHAR(255) NOT NULL,
    description            TEXT,
    quantity               DOUBLE PRECISION NOT NULL,
    unit                   VARCHAR(50) NOT NULL,
    inventory_id           BIGINT NOT NULL REFERENCES inventory_table(id) ON DELETE CASCADE
);

COMMIT;