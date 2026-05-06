<?php
$connection = pg_connect("host=localhost port=5432 dbname=postgres user=root password=DB_Admin!7890");

if(!$connection)
    echo "Failed to connect to database";

?>