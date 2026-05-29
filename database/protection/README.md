# Database Protection

This folder contains PostgreSQL guardrails for production-like databases.

Install after migrations:

```bash
psql "$DATABASE_URL" -f database/protection/install_db_protection.sql
```

What it protects:

- blocks `TRUNCATE` on operational tables;
- blocks `DROP TABLE` and `DROP SCHEMA` for protected objects;
- installs the same anti-TRUNCATE trigger on new protected tables;
- records installation status in `system_settings.db_protection.postgres` when that table exists.

What stays outside the guard by design:

- `migrations`;
- cache tables;
- queue tables;
- sessions.

Controlled restore sessions may set:

```sql
SET cobranca.restore_in_progress = 'on';
```

That bypass is intentionally session-local. It is meant for a tightly controlled restore flow, not for daily maintenance.
