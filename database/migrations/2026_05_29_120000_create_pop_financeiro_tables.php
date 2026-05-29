<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_fiscais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cobranca_id')->constrained('cobrancas')->cascadeOnDelete();
            $table->foreignUuid('boleto_id')->nullable()->constrained('boletos')->nullOnDelete();
            $table->string('numero')->nullable()->unique();
            $table->string('serie')->nullable();
            $table->string('status')->default('PENDENTE_EMISSAO')->index();
            $table->decimal('valor', 12, 2);
            $table->timestamp('emitida_em')->nullable();
            $table->date('competencia')->nullable()->index();
            $table->text('observacoes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['cobranca_id', 'status']);
        });

        Schema::create('boleto_dda_controles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('boleto_id')->unique()->constrained('boletos')->cascadeOnDelete();
            $table->string('status')->default('PENDENTE_VERIFICACAO')->index();
            $table->boolean('apareceu_no_dda')->nullable();
            $table->timestamp('verificado_em')->nullable()->index();
            $table->text('ultimo_retorno')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('serasa_ocorrencias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cobranca_id')->constrained('cobrancas')->cascadeOnDelete();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('etapa')->index();
            $table->string('status')->default('PENDENTE')->index();
            $table->timestamp('agendado_para')->nullable()->index();
            $table->timestamp('executado_em')->nullable();
            $table->text('observacoes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('pop_financeiro_checklists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('reference_date')->index();
            $table->foreignUuid('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignUuid('cobranca_id')->nullable()->constrained('cobrancas')->cascadeOnDelete();
            $table->foreignUuid('parcela_id')->nullable()->constrained('parcelas')->cascadeOnDelete();
            $table->foreignUuid('boleto_id')->nullable()->constrained('boletos')->nullOnDelete();
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('etapa')->index();
            $table->string('status')->default('PENDENTE')->index();
            $table->string('acao_canal')->nullable()->index();
            $table->string('escalonamento_nivel')->nullable()->index();
            $table->text('titulo');
            $table->text('descricao')->nullable();
            $table->timestamp('sla_limite_em')->nullable()->index();
            $table->timestamp('concluido_em')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['reference_date', 'cobranca_id', 'parcela_id', 'etapa'], 'pop_checklist_uniqueness');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pop_financeiro_checklists');
        Schema::dropIfExists('serasa_ocorrencias');
        Schema::dropIfExists('boleto_dda_controles');
        Schema::dropIfExists('notas_fiscais');
    }
};
