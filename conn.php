<?php
$connection = pg_connect("host=postgres_container port=5432 dbname=postgres user=postgres password=DB_Admin!7890");

if(!$connection)
    echo "Failed to connect to database";

?>