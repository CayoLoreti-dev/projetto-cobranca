<?php
$f = __DIR__ . '/../storage/debugbar/01KSTN2S56EKBZCKKZESMYKE4A.json';
$j = json_decode(file_get_contents($f), true);
print_r(array_keys($j));
if (isset($j['models'])) {
    print_r($j['models']);
}
if (isset($j['views'])) {
    echo "views count: ".$j['views']['count']."\n";
}
