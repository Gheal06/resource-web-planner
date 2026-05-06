<?php 

require("conn.php");

if(!$connection)
    echo "Failed to connect to database";
else {
    pg_query($connection, "CREATE TABLE IF NOT EXISTS user_table (
        user_name TEXT PRIMARY KEY,
        password_hash TEXT NOT NULL
    )");
}

?>
