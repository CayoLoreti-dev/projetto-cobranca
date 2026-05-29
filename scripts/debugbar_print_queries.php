<?php
$f = __DIR__ . '/../storage/debugbar/01KSTMGQPSV953FDG80QV0M01P.json';
$j = json_decode(file_get_contents($f), true);
foreach ($j['queries']['statements'] as $i => $s) {
    $time = isset($s['duration']) ? $s['duration'] : (isset($s['duration_str']) ? $s['duration_str'] : '?');
    $sql = isset($s['sql']) ? $s['sql'] : (isset($s['label']) ? $s['label'] : '');
    $sql = preg_replace('/\s+/', ' ', trim($sql));
    echo ($i + 1) . ". time: $time ms | sql: $sql\n";
}
echo "Total: " . $j['queries']['accumulated_duration'] . " ms\n";
