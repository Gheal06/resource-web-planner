DROP TABLE IF EXISTS resources_table;
DROP TABLE IF EXISTS user_table_permission;
DROP TABLE IF EXISTS inventory_table;
DROP TABLE IF EXISTS password_recovery_codes;
DROP TABLE IF EXISTS user_table;
CREATE TABLE user_table (
    id                    BIGSERIAL PRIMARY KEY,
    username              VARCHAR(255) UNIQUE NOT NULL,
    email                 VARCHAR(255) UNIQUE NOT NULL, 
    password_hash         VARCHAR(255) NOT NULL
);

CREATE TABLE password_recovery_codes (
    id                    BIGSERIAL PRIMARY KEY,
    user_id               BIGINT NOT NULL REFERENCES user_table(id) ON DELETE CASCADE,
    code                  VARCHAR(255) NOT NULL,
    expires_at            TIMESTAMP NOT NULL
);

CREATE TABLE inventory_table (
    id                     BIGSERIAL PRIMARY KEY,
    name                   VARCHAR(255) NOT NULL,
    description            TEXT,
    owner_id               BIGINT REFERENCES inventory_table(id) ON DELETE CASCADE
);

CREATE TABLE user_table_permission (
    id                     BIGSERIAL PRIMARY KEY,
    user_id                BIGINT NOT NULL REFERENCES user_table(id) ON DELETE CASCADE,
    inventory_id           BIGINT NOT NULL REFERENCES inventory_table(id) ON DELETE CASCADE,
    permissions            INT NOT NULL DEFAULT 0, -- insert update delete read
    CHECK ((permissions & 1) > 0 OR (permissions & 14) = 0)
);

CREATE TABLE resources_table (
    id                     BIGSERIAL PRIMARY KEY,
    name                   VARCHAR(255) NOT NULL,
    description            TEXT,
    quantity               DOUBLE PRECISION NOT NULL,
    unit                   VARCHAR(50) NOT NULL, -- ce inseamna "o unitate" in contextul acestei resurse
    inventory_id           BIGINT NOT NULL REFERENCES inventory_table(id) ON DELETE CASCADE
);

COMMIT;