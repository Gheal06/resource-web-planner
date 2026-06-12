#!/usr/bin/env python3
"""Populate the `currencies` table from a JSON file like USD.json.

Usage:
  python3 populate_currencies.py --file USD.json

You can override DB connection parameters via CLI options or env vars.
"""
import argparse
import json
import os
import sys

try:
    import psycopg2
except Exception:
    sys.exit("psycopg2 is required. Install it via pip or your package manager.")


def parse_args():
    p = argparse.ArgumentParser()
    p.add_argument("--file", "-f", required=True, help="Path to USD.json (or similar)")
    p.add_argument("--host", default=os.environ.get("PGHOST", "postgres"))
    p.add_argument("--port", default=os.environ.get("PGPORT", "5432"))
    p.add_argument("--dbname", default=os.environ.get("PGDATABASE", "rwp"))
    p.add_argument("--user", default=os.environ.get("PGUSER", "root"))
    p.add_argument("--password", default=os.environ.get("PGPASSWORD", "DB_Admin!7890"))
    return p.parse_args()


def load_codes(path):
    with open(path, "r", encoding="utf-8") as fh:
        data = json.load(fh)
    # Expecting a top-level object with `conversion_rates` mapping
    rates = data.get("conversion_rates") or data.get("rates")
    if not isinstance(rates, dict):
        raise ValueError("JSON does not contain a conversion_rates or rates mapping")
    return sorted(rates.keys())


def main():
    args = parse_args()
    codes = load_codes(args.file)

    conn = psycopg2.connect(
        host=args.host,
        port=args.port,
        dbname=args.dbname,
        user=args.user,
        password=args.password,
    )
    cur = conn.cursor()

    # Ensure table exists (safe if already created by schema)
    cur.execute(
        """
        CREATE TABLE IF NOT EXISTS currencies (
            id BIGSERIAL PRIMARY KEY,
            code VARCHAR(3) UNIQUE NOT NULL
        )
        """
    )
    conn.commit()

    # Insert codes with conflict handling
    insert_sql = "INSERT INTO currencies (code) VALUES (%s) ON CONFLICT (code) DO NOTHING"
    cur.executemany(insert_sql, [(c,) for c in codes])
    conn.commit()

    # Report
    cur.execute("SELECT COUNT(*) FROM currencies")
    total = cur.fetchone()[0]
    print(f"Processed {len(codes)} codes. currencies table now has {total} rows.")

    cur.close()
    conn.close()


if __name__ == "__main__":
    main()
