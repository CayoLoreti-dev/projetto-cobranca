<?php
$path = dirname(__DIR__) . '/database/database.sqlite';
$pdo = new PDO('sqlite:' . $path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$queries = [
    "-- Query A: list cobrancas by status\nSELECT * FROM cobrancas WHERE status = 'EMITIDO' ORDER BY created_at DESC LIMIT 20 OFFSET 0;",
    "-- Query B: list cobrancas by cliente_id\nSELECT * FROM cobrancas WHERE cliente_id = '00000000-0000-0000-0000-000000000000' ORDER BY created_at DESC LIMIT 20 OFFSET 0;",
    "-- Query C: list pop_financeiro_checklists\nSELECT * FROM pop_financeiro_checklists ORDER BY reference_date DESC LIMIT 20 OFFSET 0;",
    "-- Query D: find parcelas by cobranca_id\nSELECT * FROM parcelas WHERE cobranca_id = '00000000-0000-0000-0000-000000000000' ORDER BY numero ASC;",
];
foreach ($queries as $sql) {
    echo "\n-- Running EXPLAIN QUERY PLAN for:\n" . $sql . "\n";
    $stmt = $pdo->query('EXPLAIN QUERY PLAN ' . explode("\n", $sql, 2)[1]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo json_encode($r, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    }
}
echo "\nDone.\n";
