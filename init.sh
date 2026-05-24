#!/bin/bash
PGOPTIONS='--client-min-messages=warning' PGPASSWORD='DB_Admin!7890' psql -h 127.0.0.1 -p 7890 -d rwp -U root -f sql_scripts/init.sql # reset database