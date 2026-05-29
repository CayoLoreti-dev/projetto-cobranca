<?php

namespace App\Providers;

use App\Models\Arquivo;
use App\Models\Boleto;
use App\Models\BoletoDdaControle;
use App\Models\Cliente;
use App\Models\Cobranca;
use App\Models\Interacao;
use App\Models\NotaFiscal;
use App\Models\Parcela;
use App\Models\PopFinanceiroChecklist;
use App\Models\SerasaOcorrencia;
use App\Models\Tarefa;
use App\Models\User;
use App\Observers\AuditableObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Foundation\Console\KeyGenerateCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDestructiveCommandProtection();
        $this->configurePasswordDefaults();
        $this->configureRateLimiters();

        foreach ([
            User::class,
            Cliente::class,
            Cobranca::class,
            Parcela::class,
            Boleto::class,
            NotaFiscal::class,
            BoletoDdaControle::class,
            SerasaOcorrencia::class,
            PopFinanceiroChecklist::class,
            Tarefa::class,
            Interacao::class,
            Arquivo::class,
        ] as $model) {
            $model::observe(AuditableObserver::class);
        }

        // In local/debug, attach a DB listener to collect query stats per request
        if (app()->environment('local') || config('app.debug')) {
            $queries = 0;
            $time = 0.0;
            $collected = [];

            DB::listen(function ($query) use (&$queries, &$time, &$collected) {
                $queries++;
                $time += $query->time ?? 0;

                // collect a filtered backtrace focused on application files (app/, routes/, resources/, database/)
                $raw = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 50);
                $filtered = [];
                $appPaths = [
                    base_path('app'),
                    base_path('routes'),
                    base_path('resources'),
                    base_path('database'),
                ];

                foreach ($raw as $frame) {
                    if (empty($frame['file'])) {
                        continue;
                    }

                    foreach ($appPaths as $p) {
                        if (str_contains($frame['file'], $p)) {
                            $filtered[] = [
                                'file' => $frame['file'] ?? null,
                                'line' => $frame['line'] ?? null,
                                'function' => $frame['function'] ?? null,
                                'class' => $frame['class'] ?? null,
                                'type' => $frame['type'] ?? null,
                            ];
                            break;
                        }
                    }

                    if (count($filtered) >= 8) {
                        break;
                    }
                }

                $collected[] = [
                    'sql' => $query->sql ?? '',
                    'bindings' => $query->bindings ?? [],
                    'time' => $query->time ?? 0,
                    'connection' => $query->connectionName ?? null,
                    'backtrace' => $filtered,
                ];
            });

            app()->terminating(function () use (&$queries, &$time, &$collected) {
                try {
                    // legacy summary
                    \Illuminate\Support\Facades\Log::channel('single')->info('QueryStats', [
                        'uri' => request()?->getPathInfo(),
                        'queries' => $queries,
                        'queries_time_ms' => round($time, 2),
                    ]);

                    // write detailed per-request queries to storage for offline analysis
                    $dir = storage_path('debug_queries');
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0775, true);
                    }
                    $id = date('Ymd_His') . '_' . substr(sha1(uniqid('', true)), 0, 8);
                    $file = $dir . DIRECTORY_SEPARATOR . $id . '.json';
                    $payload = [
                        'uri' => request()?->getPathInfo(),
                        'timestamp' => now()->toDateTimeString(),
                        'queries' => $queries,
                        'queries_time_ms' => round($time, 2),
                        'statements' => $collected,
                    ];
                    @file_put_contents($file, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                } catch (\Throwable $e) {
                    // ignore logging errors in terminating
                }
            });
        }
    }

    private function configureDestructiveCommandProtection(): void
    {
        $isProduction = $this->app->isProduction();

        DB::prohibitDestructiveCommands($isProduction);
        SeedCommand::prohibit($isProduction);
        KeyGenerateCommand::prohibit($isProduction);
    }

    private function configurePasswordDefaults(): void
    {
        Password::defaults(function (): Password {
            $rule = Password::min(config('security.password.min', 12));

            if (config('security.password.mixed_case', true)) {
                $rule->mixedCase();
            }

            if (config('security.password.numbers', true)) {
                $rule->numbers();
            }

            if (config('security.password.symbols', true)) {
                $rule->symbols();
            }

            if (config('security.password.uncompromised', false)) {
                $rule->uncompromised();
            }

            return $rule;
        });
    }

    private function configureRateLimiters(): void
    {
        RateLimiter::for('api-token', fn (Request $request): array => [
            Limit::perMinute(config('security.rate_limits.api_token_per_minute', 5))
                ->by(Str::lower((string) $request->input('email')).'|'.$request->ip()),
        ]);

        RateLimiter::for('api-read', fn (Request $request): array => [
            Limit::perMinute(config('security.rate_limits.api_read_per_minute', 120))
                ->by($this->rateLimitKey($request, 'read')),
        ]);

        RateLimiter::for('api-write', fn (Request $request): array => [
            Limit::perMinute(config('security.rate_limits.api_write_per_minute', 30))
                ->by($this->rateLimitKey($request, 'write')),
        ]);

        RateLimiter::for('api-report', fn (Request $request): array => [
            Limit::perMinute(config('security.rate_limits.api_report_per_minute', 20))
                ->by($this->rateLimitKey($request, 'report')),
        ]);
    }

    private function rateLimitKey(Request $request, string $scope): string
    {
        return $scope.'|'.($request->user()?->getAuthIdentifier() ?? $request->ip());
    }
}
