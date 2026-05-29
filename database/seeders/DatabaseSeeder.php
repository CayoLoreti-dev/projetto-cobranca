<?php

namespace Database\Seeders;

use App\Actions\Cobrancas\CreateCobrancaAction;
use App\Enums\CobrancaStatus;
use App\Enums\CobrancaTipo;
use App\Enums\ParcelaStatus;
use App\Models\Cliente;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'admin.access',
            'usuarios.view', 'usuarios.create', 'usuarios.update',
            'clientes.view', 'clientes.create', 'clientes.update', 'clientes.archive',
            'cobrancas.view', 'cobrancas.create', 'cobrancas.update', 'cobrancas.archive',
            'parcelas.view', 'parcelas.update', 'parcelas.pay',
            'boletos.view', 'boletos.update', 'boletos.upload',
            'tarefas.view', 'tarefas.update',
            'interacoes.view', 'interacoes.create',
            'auditoria.view',
            'relatorios.view',
            'settings.view', 'settings.update',
            'notas_fiscais.view', 'notas_fiscais.create', 'notas_fiscais.update',
            'dda.view', 'dda.update',
            'serasa.view', 'serasa.update',
            'pop_financeiro.view', 'pop_financeiro.update',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        Role::findOrCreate('ADMIN')->syncPermissions($permissions);
        Role::findOrCreate('FINANCEIRO')->syncPermissions([
            'admin.access', 'clientes.view', 'clientes.create', 'clientes.update',
            'cobrancas.view', 'cobrancas.create', 'cobrancas.update',
            'parcelas.view', 'parcelas.update', 'parcelas.pay',
            'boletos.view', 'boletos.update', 'boletos.upload',
            'tarefas.view', 'tarefas.update', 'interacoes.view', 'interacoes.create',
            'relatorios.view',
            'notas_fiscais.view', 'notas_fiscais.create', 'notas_fiscais.update',
            'dda.view', 'dda.update',
            'serasa.view', 'serasa.update',
            'pop_financeiro.view', 'pop_financeiro.update',
        ]);
        Role::findOrCreate('OPERADOR')->syncPermissions([
            'admin.access', 'clientes.view', 'cobrancas.view', 'parcelas.view',
            'boletos.view', 'tarefas.view', 'tarefas.update', 'interacoes.view', 'interacoes.create',
            'notas_fiscais.view', 'dda.view', 'serasa.view', 'pop_financeiro.view',
        ]);
        Role::findOrCreate('LEITURA')->syncPermissions([
            'admin.access', 'clientes.view', 'cobrancas.view', 'parcelas.view', 'boletos.view',
            'tarefas.view', 'interacoes.view', 'relatorios.view',
            'notas_fiscais.view', 'dda.view', 'serasa.view', 'pop_financeiro.view',
        ]);

        $admin = User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@cobranca.local',
            'password' => Hash::make('Cobranca!2026'),
            'is_active' => true,
        ]);
        $admin->assignRole('ADMIN');

        $financeiro = User::factory()->create([
            'name' => 'Setor Financeiro',
            'email' => 'financeiro@cobranca.local',
            'password' => Hash::make('Cobranca!2026'),
            'is_active' => true,
        ]);
        $financeiro->assignRole('FINANCEIRO');

        SystemSetting::updateOrCreate(['key' => 'billing.alert_days'], [
            'key' => 'billing.alert_days',
            'group' => 'cobranca',
            'type' => 'array',
            'value' => [-10, -5, 5, 10, 30],
            'description' => 'Dias relativos ao vencimento usados para agenda automatica.',
            'updated_by_id' => $admin->id,
        ]);

        SystemSetting::updateOrCreate(['key' => 'db_protection.postgres'], [
            'key' => 'db_protection.postgres',
            'group' => 'db_protection',
            'type' => 'array',
            'value' => [
                'status' => 'pending_postgres',
                'script' => 'database/protection/install_db_protection.sql',
            ],
            'description' => 'Status da instalacao das travas PostgreSQL contra TRUNCATE/DROP.',
            'updated_by_id' => $admin->id,
        ]);

        SystemSetting::updateOrCreate(['key' => 'pop.templates.envio_inicial'], [
            'key' => 'pop.templates.envio_inicial',
            'group' => 'pop_financeiro',
            'type' => 'array',
            'value' => [
                'titulo' => 'Envio inicial de boleto',
                'mensagem' => 'Template placeholder para envio inicial. (Sem envio automático ativo)',
                'canal_preferencial' => 'EMAIL',
            ],
            'description' => 'Template POP para envio inicial (skeleton).',
            'updated_by_id' => $admin->id,
        ]);

        SystemSetting::updateOrCreate(['key' => 'pop.templates.cobranca_5_dias'], [
            'key' => 'pop.templates.cobranca_5_dias',
            'group' => 'pop_financeiro',
            'type' => 'array',
            'value' => [
                'titulo' => 'Cobrança 5 dias',
                'mensagem' => 'Template placeholder para cobrança de 5 dias. (Sem envio automático ativo)',
                'canal_preferencial' => 'EMAIL',
            ],
            'description' => 'Template POP para 5 dias de atraso (skeleton).',
            'updated_by_id' => $admin->id,
        ]);

        SystemSetting::updateOrCreate(['key' => 'pop.templates.cobranca_10_dias'], [
            'key' => 'pop.templates.cobranca_10_dias',
            'group' => 'pop_financeiro',
            'type' => 'array',
            'value' => [
                'titulo' => 'Cobrança 10 dias',
                'mensagem' => 'Template placeholder para cobrança de 10 dias. (Sem envio automático ativo)',
                'canal_preferencial' => 'EMAIL',
            ],
            'description' => 'Template POP para 10 dias de atraso (skeleton).',
            'updated_by_id' => $admin->id,
        ]);

        SystemSetting::updateOrCreate(['key' => 'pop.templates.cobranca_30_dias'], [
            'key' => 'pop.templates.cobranca_30_dias',
            'group' => 'pop_financeiro',
            'type' => 'array',
            'value' => [
                'titulo' => 'Cobrança 30 dias',
                'mensagem' => 'Template placeholder para cobrança de 30 dias/negativação formal. (Sem envio automático ativo)',
                'canal_preferencial' => 'EMAIL',
            ],
            'description' => 'Template POP para 30 dias de atraso (skeleton).',
            'updated_by_id' => $admin->id,
        ]);

        SystemSetting::updateOrCreate(['key' => 'ap_vistoria.institucional'], [
            'key' => 'ap_vistoria.institucional',
            'group' => 'institucional',
            'type' => 'array',
            'value' => [
                'razao_social' => 'AP Vistoria (placeholder)',
                'cnpj' => '00.000.000/0000-00',
                'oia' => 'OIA-PLACEHOLDER',
                'convenio_ceg_naturgy' => 'CONVENIO-PLACEHOLDER',
                'responsavel_legal' => 'RESPONSAVEL-LEGAL-PLACEHOLDER',
            ],
            'description' => 'Dados institucionais da AP Vistoria para o POP Financeiro.',
            'updated_by_id' => $admin->id,
        ]);

        $cliente = Cliente::factory()->create([
            'nome' => 'Condominio Residencial Atlantico',
            'tipo' => 'CONDOMINIO',
            'documento' => '12.345.678/0001-90',
            'responsavel_financeiro' => 'Larissa Menezes',
            'email' => 'financeiro@atlantico.local',
            'created_by_id' => $financeiro->id,
            'updated_by_id' => $financeiro->id,
        ]);

        app(CreateCobrancaAction::class)->execute([
            'cliente_id' => $cliente->id,
            'categoria' => 'vistoria',
            'tipo' => CobrancaTipo::Parcelado->value,
            'valor_total' => 12840.50,
            'status' => CobrancaStatus::Preventiva->value,
            'data_emissao' => now()->subDays(10)->toDateString(),
            'data_vencimento_principal' => now()->addDays(5)->toDateString(),
            'responsavel_id' => $financeiro->id,
            'prioridade' => 3,
            'parcelas' => [
                ['numero' => 1, 'valor' => 4280.17, 'vencimento' => now()->subDays(5)->toDateString(), 'status' => ParcelaStatus::EmAtraso->value],
                ['numero' => 2, 'valor' => 4280.17, 'vencimento' => now()->addDays(5)->toDateString(), 'status' => ParcelaStatus::Pendente->value],
                ['numero' => 3, 'valor' => 4280.16, 'vencimento' => now()->addMonth()->toDateString(), 'status' => ParcelaStatus::Pendente->value],
            ],
            'created_by_id' => $financeiro->id,
            'updated_by_id' => $financeiro->id,
        ], gerarBoletos: false);
    }
}
