#!/bin/bash
if [ $# -ne 0 ]; then
    PGOPTIONS='--client-min-messages=warning' PGPASSWORD='DB_Admin!7890' psql -h postgres -p 5432 -d rwp -U root -f sql_scripts/init.sql 
fi
PGOPTIONS='--client-min-messages=warning' PGPASSWORD='DB_Admin!7890' psql -h postgres -p 5432 -d rwp -U root -f sql_scripts/users.sql 
PGOPTIONS='--client-min-messages=warning' PGPASSWORD='DB_Admin!7890' psql -h postgres -p 5432 -d rwp -U root -f sql_scripts/transactions.sql 
if [ $# -ne 0 ]; then
    python3 populate_currencies.py --file USD.json
    PGOPTIONS='--client-min-messages=warning' PGPASSWORD='DB_Admin!7890' psql -h postgres -p 5432 -d rwp -U root -f sql_scripts/populate.sql 
fi