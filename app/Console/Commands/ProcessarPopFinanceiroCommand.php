<?php

namespace App\Console\Commands;

use App\Support\Billing\PopFinanceiroService;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

class ProcessarPopFinanceiroCommand extends Command
{
    protected $signature = 'pop:financeiro:processar {--date= : Data de referência no formato Y-m-d}';

    protected $description = 'Processa a régua operacional do POP Financeiro (skeleton sem envio automático).';

    public function handle(PopFinanceiroService $service): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : now();

        $summary = $service->runDaily($date);

        $this->info('POP Financeiro processado com sucesso.');
        foreach ($summary as $key => $value) {
            $this->line(sprintf('- %s: %d', $key, $value));
        }

        return self::SUCCESS;
    }
}
