<?php

namespace App\Providers;

use App\Models\Arquivo;
use App\Models\BoletoDdaControle;
use App\Models\Boleto;
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
use Illuminate\Support\ServiceProvider;

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
}
