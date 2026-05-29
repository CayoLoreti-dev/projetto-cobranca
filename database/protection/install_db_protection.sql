-- Cobranca Platform - PostgreSQL destructive-operation protection
--
-- Run after migrations, connected to the target database as a role allowed to
-- create triggers, functions and event triggers. PostgreSQL event triggers
-- normally require superuser privileges.
--
-- Restore bypass, only for a controlled pg_restore session:
--   SET cobranca.restore_in_progress = 'on';

CREATE OR REPLACE FUNCTION public.cobranca_restore_bypass_enabled()
RETURNS boolean
LANGUAGE sql
STABLE
AS $$
    SELECT COALESCE(current_setting('cobranca.restore_in_progress', true), '') = 'on';
$$;

CREATE OR REPLACE FUNCTION public.cobranca_should_protect_table(
    target_schema text,
    target_table text
)
RETURNS boolean
LANGUAGE sql
IMMUTABLE
AS $$
    SELECT
        target_schema = 'public'
        AND target_table <> ALL (ARRAY[
            'migrations',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'sessions'
        ]);
$$;

CREATE OR REPLACE FUNCTION public.cobranca_prevent_truncate()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
    IF public.cobranca_restore_bypass_enabled() THEN
        RETURN NULL;
    END IF;

    RAISE EXCEPTION 'TRUNCATE blocked on protected table %.%', TG_TABLE_SCHEMA, TG_TABLE_NAME
        USING ERRCODE = 'insufficient_privilege';
END;
$$;

DO $$
DECLARE
    target record;
BEGIN
    FOR target IN
        SELECT namespace.nspname AS schema_name, class.relname AS table_name
        FROM pg_class class
        JOIN pg_namespace namespace ON namespace.oid = class.relnamespace
        WHERE class.relkind IN ('r', 'p')
          AND namespace.nspname = 'public'
    LOOP
        IF public.cobranca_should_protect_table(target.schema_name, target.table_name) THEN
            EXECUTE format(
                'DROP TRIGGER IF EXISTS cobranca_prevent_truncate_guard ON %I.%I',
                target.schema_name,
                target.table_name
            );

            EXECUTE format(
                'CREATE TRIGGER cobranca_prevent_truncate_guard BEFORE TRUNCATE ON %I.%I FOR EACH STATEMENT EXECUTE FUNCTION public.cobranca_prevent_truncate()',
                target.schema_name,
                target.table_name
            );
        END IF;
    END LOOP;
END;
$$;

CREATE OR REPLACE FUNCTION public.cobranca_block_destructive_drop()
RETURNS event_trigger
LANGUAGE plpgsql
AS $$
DECLARE
    dropped record;
BEGIN
    IF public.cobranca_restore_bypass_enabled() THEN
        RETURN;
    END IF;

    FOR dropped IN SELECT * FROM pg_event_trigger_dropped_objects()
    LOOP
        IF dropped.object_type = 'schema' AND dropped.object_name = 'public' THEN
            RAISE EXCEPTION 'DROP SCHEMA blocked on protected schema %', dropped.object_name
                USING ERRCODE = 'insufficient_privilege';
        END IF;

        IF dropped.object_type = 'table'
            AND public.cobranca_should_protect_table(dropped.schema_name, dropped.object_name) THEN
            RAISE EXCEPTION 'DROP TABLE blocked on protected table %.%', dropped.schema_name, dropped.object_name
                USING ERRCODE = 'insufficient_privilege';
        END IF;
    END LOOP;
END;
$$;

DROP EVENT TRIGGER IF EXISTS cobranca_block_destructive_drop;
CREATE EVENT TRIGGER cobranca_block_destructive_drop
    ON sql_drop
    WHEN TAG IN ('DROP TABLE', 'DROP SCHEMA')
    EXECUTE FUNCTION public.cobranca_block_destructive_drop();

CREATE OR REPLACE FUNCTION public.cobranca_install_truncate_guard_for_new_tables()
RETURNS event_trigger
LANGUAGE plpgsql
AS $$
DECLARE
    command record;
    table_schema text;
    table_name text;
BEGIN
    IF public.cobranca_restore_bypass_enabled() THEN
        RETURN;
    END IF;

    FOR command IN SELECT * FROM pg_event_trigger_ddl_commands()
    LOOP
        IF command.object_type = 'table' THEN
            SELECT namespace.nspname, class.relname
            INTO table_schema, table_name
            FROM pg_class class
            JOIN pg_namespace namespace ON namespace.oid = class.relnamespace
            WHERE class.oid = command.objid
              AND class.relkind IN ('r', 'p');

            IF table_name IS NOT NULL
                AND public.cobranca_should_protect_table(table_schema, table_name)
                AND NOT EXISTS (
                    SELECT 1
                    FROM pg_trigger
                    WHERE tgrelid = command.objid
                      AND tgname = 'cobranca_prevent_truncate_guard'
                      AND NOT tgisinternal
                ) THEN
                EXECUTE format(
                    'CREATE TRIGGER cobranca_prevent_truncate_guard BEFORE TRUNCATE ON %I.%I FOR EACH STATEMENT EXECUTE FUNCTION public.cobranca_prevent_truncate()',
                    table_schema,
                    table_name
                );
            END IF;
        END IF;
    END LOOP;
END;
$$;

DROP EVENT TRIGGER IF EXISTS cobranca_install_truncate_guard_for_new_tables;
CREATE EVENT TRIGGER cobranca_install_truncate_guard_for_new_tables
    ON ddl_command_end
    WHEN TAG IN ('CREATE TABLE', 'CREATE TABLE AS')
    EXECUTE FUNCTION public.cobranca_install_truncate_guard_for_new_tables();

DO $$
BEGIN
    IF to_regclass('public.system_settings') IS NOT NULL THEN
        INSERT INTO public.system_settings (
            key,
            "group",
            value,
            type,
            description,
            is_encrypted,
            created_at,
            updated_at
        )
        VALUES (
            'db_protection.postgres',
            'db_protection',
            json_build_object('status', 'applied', 'applied_at', now(), 'script', 'install_db_protection.sql'),
            'array',
            'PostgreSQL TRUNCATE/DROP protection installed.',
            false,
            now(),
            now()
        )
        ON CONFLICT (key) DO UPDATE SET
            value = EXCLUDED.value,
            description = EXCLUDED.description,
            updated_at = now();
    END IF;
END;
$$;

SELECT
    namespace.nspname AS schema_name,
    class.relname AS table_name,
    EXISTS (
        SELECT 1
        FROM pg_trigger trigger
        WHERE trigger.tgrelid = class.oid
          AND trigger.tgname = 'cobranca_prevent_truncate_guard'
          AND NOT trigger.tgisinternal
    ) AS has_truncate_guard
FROM pg_class class
JOIN pg_namespace namespace ON namespace.oid = class.relnamespace
WHERE class.relkind IN ('r', 'p')
  AND namespace.nspname = 'public'
  AND public.cobranca_should_protect_table(namespace.nspname, class.relname)
ORDER BY class.relname;
