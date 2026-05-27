#!/bin/bash
if [ $# -ne 0 ]; then
    PGOPTIONS='--client-min-messages=warning' PGPASSWORD='DB_Admin!7890' psql -h 127.0.0.1 -p 7890 -d rwp -U root -f sql_scripts/init.sql # reset database
fi
PGOPTIONS='--client-min-messages=warning' PGPASSWORD='DB_Admin!7890' psql -h 127.0.0.1 -p 7890 -d rwp -U root -f sql_scripts/users.sql # user functions
if [ $# -ne 0 ]; then
    python3 populate_currencies.py --file USD.json
    PGOPTIONS='--client-min-messages=warning' PGPASSWORD='DB_Admin!7890' psql -h 127.0.0.1 -p 7890 -d rwp -U root -f sql_scripts/populate.sql # populate database
fi
