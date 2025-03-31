# Database Structure

## Overview
This directory contains all database-related files for the VSModDB project, including migrations and schema definitions.

## Directory Structure
- `/migrations`: Contains all database migration files in sequential order
  - `000_tables.sql`: Initial database schema
  - `103_migrate.sql`: Team members table modifications
  - `104_migrate.sql`: File table imagesize addition
  - `105_migrate.sql`: Mod logo file structure updates

## Migration Naming Convention
Migration files follow a sequential numbering system (e.g., 000, 103, 104, 105) to ensure proper execution order.

## Running Migrations
Migrations are SQL files containing stored procedures that check for existing conditions before applying changes, ensuring idempotency and safe execution.

## Important Notes
- All migrations use stored procedures with proper error handling
- Transactions are used where appropriate to ensure data integrity
- Each migration checks for existing conditions before applying changes
- Migrations are designed to be idempotent (can be run multiple times safely)