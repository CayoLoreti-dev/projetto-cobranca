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
