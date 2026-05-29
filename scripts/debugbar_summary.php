<?php
$files = [
    __DIR__ . '/../storage/debugbar/01KSTMGQPSV953FDG80QV0M01P.json',
    __DIR__ . '/../storage/debugbar/01KSTMH1AVZNE63CCA53118KT6.json',
];
foreach ($files as $f) {
    if (!file_exists($f)) { echo "missing $f\n"; continue; }
    $j = json_decode(file_get_contents($f), true);
    echo "FILE: $f\n";
    if (isset($j['collectors']['db'])) {
        $db = $j['collectors']['db'];
        if (isset($db['data']['statements'])) {
            echo 'queries: ' . count($db['data']['statements']) . "\n";
            foreach ($db['data']['statements'] as $i => $s) {
                $time = isset($s['duration']) ? $s['duration'] : (isset($s['duration_str']) ? $s['duration_str'] : '?');
                $sql = isset($s['sql']) ? $s['sql'] : (isset($s['label']) ? $s['label'] : '');
                $sql = preg_replace('/\s+/', ' ', trim($sql));
                echo ($i + 1) . ". time: $time ms | sql: $sql\n";
                if ($i >= 29) { echo "...\n"; break; }
            }
        } else { echo "no statements key\n"; }
    } else { echo "no db collector\n"; }
    echo "\n";
}

// Also print summary of total query time if present
foreach ($files as $f) {
    if (!file_exists($f)) continue;
    $j = json_decode(file_get_contents($f), true);
    echo "Summary for $f:\n";
    $total = 0.0;
    if (isset($j['collectors']['db']['data']['statements'])) {
        foreach ($j['collectors']['db']['data']['statements'] as $s) {
            if (isset($s['duration'])) $total += floatval($s['duration']);
        }
    }
    echo "total_query_time_ms: $total\n\n";
}
