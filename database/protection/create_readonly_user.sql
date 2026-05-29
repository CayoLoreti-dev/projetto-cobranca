-- Cobranca Platform - PostgreSQL read-only user for DBeaver
--
-- Run with a role that can create roles and grant privileges.
--
-- Usage:
--   psql "$DATABASE_URL" -f database/protection/create_readonly_user.sql

\set readonly_user 'cobranca_readonly'
\prompt 'Password for cobranca_readonly: ' readonly_password

SELECT format(
    'CREATE ROLE %I LOGIN PASSWORD %L',
    :'readonly_user',
    :'readonly_password'
)
WHERE NOT EXISTS (
    SELECT 1
    FROM pg_roles
    WHERE rolname = :'readonly_user'
)
\gexec

SELECT format(
    'ALTER ROLE %I LOGIN PASSWORD %L NOSUPERUSER NOCREATEDB NOCREATEROLE NOREPLICATION',
    :'readonly_user',
    :'readonly_password'
)
\gexec

SELECT format(
    'GRANT CONNECT ON DATABASE %I TO %I',
    current_database(),
    :'readonly_user'
)
\gexec

GRANT USAGE ON SCHEMA public TO :"readonly_user";
GRANT SELECT ON ALL TABLES IN SCHEMA public TO :"readonly_user";
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO :"readonly_user";

ALTER DEFAULT PRIVILEGES IN SCHEMA public
    GRANT SELECT ON TABLES TO :"readonly_user";

ALTER DEFAULT PRIVILEGES IN SCHEMA public
    GRANT USAGE, SELECT ON SEQUENCES TO :"readonly_user";

SELECT
    :'readonly_user' AS role_name,
    current_database() AS database_name,
    'read-only grants applied' AS status;
