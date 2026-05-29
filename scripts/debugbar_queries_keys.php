<?php
$f = __DIR__ . '/../storage/debugbar/01KSTMGQPSV953FDG80QV0M01P.json';
$j = json_decode(file_get_contents($f), true);
print_r(array_keys($j['queries']));
