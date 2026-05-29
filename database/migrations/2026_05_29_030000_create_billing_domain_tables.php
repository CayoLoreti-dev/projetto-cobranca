<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nome')->index();
            $table->string('tipo');
            $table->string('documento')->unique();
            $table->string('responsavel_financeiro');
            $table->string('email')->index();
            $table->string('telefone')->index();
            $table->string('whatsapp')->nullable();
            $table->text('endereco')->nullable();
            $table->string('status')->default('ATIVO')->index();
            $table->text('observacoes')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cobrancas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('codigo')->unique();
            $table->string('categoria')->nullable()->index();
            $table->string('tipo');
            $table->decimal('valor_total', 12, 2);
            $table->string('moeda', 3)->default('BRL');
            $table->string('status')->default('EMITIDA')->index();
            $table->date('data_emissao');
            $table->date('data_vencimento_principal')->index();
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('prioridade')->default(2)->index();
            $table->text('proxima_acao')->nullable();
            $table->date('data_proxima_acao')->nullable();
            $table->text('observacoes')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['cliente_id', 'status']);
        });

        Schema::create('parcelas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cobranca_id')->constrained('cobrancas')->cascadeOnDelete();
            $table->unsignedInteger('numero');
            $table->decimal('valor', 12, 2);
            $table->date('vencimento')->index();
            $table->string('status')->default('PENDENTE')->index();
            $table->timestamp('paga_em')->nullable();
            $table->decimal('valor_pago', 12, 2)->nullable();
            $table->string('forma_pagamento')->nullable();
            $table->text('observacoes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['cobranca_id', 'numero']);
        });

        Schema::create('arquivos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('checksum_sha256', 64)->nullable()->index();
            $table->foreignId('uploaded_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableUuidMorphs('fileable');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('boletos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parcela_id')->nullable()->unique()->constrained('parcelas')->nullOnDelete();
            $table->foreignUuid('cobranca_id')->nullable()->constrained('cobrancas')->cascadeOnDelete();
            $table->foreignUuid('pdf_file_id')->nullable()->constrained('arquivos')->nullOnDelete();
            $table->string('linha_digitavel')->nullable();
            $table->string('codigo_barras')->nullable();
            $table->decimal('valor', 12, 2);
            $table->date('vencimento')->index();
            $table->string('status')->default('EMITIDO')->index();
            $table->timestamp('gerado_em')->nullable();
            $table->timestamp('enviado_em')->nullable();
            $table->timestamp('lido_em')->nullable();
            $table->timestamp('recebido_em')->nullable();
            $table->timestamp('pago_em')->nullable();
            $table->string('pdf_url')->nullable();
            $table->text('observacoes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'vencimento']);
        });

        Schema::create('tarefas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignUuid('cobranca_id')->nullable()->constrained('cobrancas')->cascadeOnDelete();
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo')->index();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->unsignedTinyInteger('prioridade')->default(2)->index();
            $table->string('status')->default('ABERTA')->index();
            $table->timestamp('vence_em')->nullable()->index();
            $table->timestamp('iniciada_em')->nullable();
            $table->timestamp('concluida_em')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('interacoes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignUuid('cobranca_id')->nullable()->constrained('cobrancas')->cascadeOnDelete();
            $table->foreignUuid('parcela_id')->nullable()->constrained('parcelas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('canal')->index();
            $table->string('resultado')->nullable()->index();
            $table->string('assunto')->nullable();
            $table->text('descricao');
            $table->timestamp('ocorreu_em')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['cliente_id', 'ocorreu_em']);
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('group')->default('geral')->index();
            $table->json('value');
            $table->string('type')->default('string');
            $table->text('description')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('auditable_type')->nullable();
            $table->string('auditable_id')->nullable();
            $table->string('action')->index();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->text('reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('origin')->default('system')->index();
            $table->string('checksum_sha256', 64)->nullable();
            $table->timestamp('occurred_at')->index();
            $table->index(['auditable_type', 'auditable_id']);
        });

        Schema::create('cobranca_eventos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cobranca_id')->constrained('cobrancas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo')->index();
            $table->json('payload')->nullable();
            $table->string('checksum_sha256', 64)->nullable();
            $table->timestamp('occurred_at')->index();
        });

        Schema::create('parcela_eventos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parcela_id')->constrained('parcelas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo')->index();
            $table->json('payload')->nullable();
            $table->string('checksum_sha256', 64)->nullable();
            $table->timestamp('occurred_at')->index();
        });

        Schema::create('boleto_eventos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('boleto_id')->constrained('boletos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipo')->index();
            $table->json('payload')->nullable();
            $table->string('checksum_sha256', 64)->nullable();
            $table->timestamp('occurred_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boleto_eventos');
        Schema::dropIfExists('parcela_eventos');
        Schema::dropIfExists('cobranca_eventos');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('interacoes');
        Schema::dropIfExists('tarefas');
        Schema::dropIfExists('boletos');
        Schema::dropIfExists('arquivos');
        Schema::dropIfExists('parcelas');
        Schema::dropIfExists('cobrancas');
        Schema::dropIfExists('clientes');
    }
};
