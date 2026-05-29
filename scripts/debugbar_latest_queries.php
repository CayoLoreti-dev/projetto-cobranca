<?php
$dir = __DIR__ . '/../storage/debugbar';
$files = glob($dir . '/*.json');
if (!$files) { echo "no files\n"; exit; }
usort($files, function($a,$b){ return filemtime($a) - filemtime($b); });
$f = end($files);
echo "Using file: $f\n";
$j = json_decode(file_get_contents($f), true);
if (!isset($j['queries'])) { echo "no queries\n"; exit; }
echo "accumulated_duration=".($j['queries']['accumulated_duration'] ?? 'n/a')."\n";
foreach ($j['queries']['statements'] as $i => $s) {
    $time = $s['duration'] ?? ($s['duration_str'] ?? '?');
    $sql = $s['sql'] ?? ($s['label'] ?? '');
    $sql = preg_replace('/\s+/', ' ', trim($sql));
    echo ($i+1).". time: $time ms | sql: $sql\n";
}
