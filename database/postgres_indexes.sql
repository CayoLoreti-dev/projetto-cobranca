-- PostgreSQL indexes aligned with the current Laravel migrations.
-- Safe to run manually on staging/production after reviewing execution plans.

CREATE INDEX IF NOT EXISTS cobrancas_status_created_at_idx
    ON cobrancas (status, created_at DESC);

CREATE INDEX IF NOT EXISTS cobrancas_cliente_created_at_idx
    ON cobrancas (cliente_id, created_at DESC);

CREATE INDEX IF NOT EXISTS audit_logs_checksum_sha256_idx
    ON audit_logs (checksum_sha256);

CREATE INDEX IF NOT EXISTS cobranca_eventos_checksum_sha256_idx
    ON cobranca_eventos (checksum_sha256);

CREATE INDEX IF NOT EXISTS parcela_eventos_checksum_sha256_idx
    ON parcela_eventos (checksum_sha256);

CREATE INDEX IF NOT EXISTS boleto_eventos_checksum_sha256_idx
    ON boleto_eventos (checksum_sha256);
