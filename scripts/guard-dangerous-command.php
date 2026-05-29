<?php

$subjects = [];
$subjects[] = implode(' ', array_slice($argv, 1));

foreach (array_slice($argv, 1) as $argument) {
    if (! is_file($argument)) {
        continue;
    }

    $extension = strtolower(pathinfo($argument, PATHINFO_EXTENSION));

    if (! in_array($extension, ['sql', 'php', 'sh', 'ps1', 'bat', 'cmd', 'txt'], true)) {
        continue;
    }

    $contents = file_get_contents($argument);

    if ($contents !== false) {
        $subjects[] = "file:{$argument}\n".$contents;
    }
}

$patterns = [
    'destructive artisan command' => '/\b(?:php\s+)?artisan\s+(?:migrate:(?:fresh|refresh|reset|rollback)|db:wipe)\b/i',
    'database seeder command' => '/\b(?:php\s+)?artisan\s+db:seed\b/i',
    'drop table or schema' => '/\bdrop\s+(?:table|schema)\b/i',
    'truncate table' => '/\btruncate\s+(?:table\s+)?[\w."`]+/i',
];

$blocked = [];

foreach ($subjects as $subject) {
    foreach ($patterns as $label => $pattern) {
        if (preg_match($pattern, $subject)) {
            $blocked[] = $label;
        }
    }

    foreach (findUnfilteredStatements($subject, 'delete from') as $statement) {
        $blocked[] = 'DELETE without WHERE: '.preview($statement);
    }

    foreach (findUnfilteredStatements($subject, 'update') as $statement) {
        $blocked[] = 'UPDATE without WHERE: '.preview($statement);
    }
}

$blocked = array_values(array_unique($blocked));

if ($blocked === []) {
    exit(0);
}

$message = 'Blocked dangerous command/content: '.implode('; ', $blocked);
logGuardDecision($message, $argv);

fwrite(STDERR, $message.PHP_EOL);
exit(1);

function findUnfilteredStatements(string $subject, string $verb): array
{
    preg_match_all('/\b'.preg_quote($verb, '/').'\b.+?(?:;|$)/is', $subject, $matches);

    return array_values(array_filter($matches[0], fn (string $statement): bool => ! preg_match('/\bwhere\b/i', $statement)));
}

function preview(string $statement): string
{
    $statement = preg_replace('/\s+/', ' ', trim($statement)) ?? $statement;

    return substr($statement, 0, 120);
}

function logGuardDecision(string $message, array $argv): void
{
    $directory = __DIR__.'/../storage/logs';

    if (! is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    file_put_contents(
        $directory.'/dangerous-command-guard.log',
        json_encode([
            'occurred_at' => date(DATE_ATOM),
            'message' => $message,
            'argv' => $argv,
        ], JSON_UNESCAPED_SLASHES).PHP_EOL,
        FILE_APPEND
    );
}
