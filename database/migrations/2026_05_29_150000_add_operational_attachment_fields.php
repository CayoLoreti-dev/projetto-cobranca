<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boletos', function (Blueprint $table): void {
            $table->string('pdf_path')->nullable()->after('pdf_url');
            $table->string('pdf_original_name')->nullable()->after('pdf_path');
        });

        Schema::table('notas_fiscais', function (Blueprint $table): void {
            $table->string('pdf_path')->nullable()->after('competencia');
            $table->string('pdf_original_name')->nullable()->after('pdf_path');
        });

        Schema::table('serasa_ocorrencias', function (Blueprint $table): void {
            $table->string('protocolo')->nullable()->index()->after('status');
            $table->string('documento_devedor')->nullable()->index()->after('protocolo');
            $table->decimal('valor_negativado', 12, 2)->nullable()->after('documento_devedor');
            $table->date('data_limite_regularizacao')->nullable()->index()->after('valor_negativado');
            $table->timestamp('data_baixa')->nullable()->index()->after('executado_em');
            $table->string('motivo_baixa')->nullable()->after('data_baixa');
        });
    }

    public function down(): void
    {
        Schema::table('serasa_ocorrencias', function (Blueprint $table): void {
            $table->dropColumn([
                'protocolo',
                'documento_devedor',
                'valor_negativado',
                'data_limite_regularizacao',
                'data_baixa',
                'motivo_baixa',
            ]);
        });

        Schema::table('notas_fiscais', function (Blueprint $table): void {
            $table->dropColumn([
                'pdf_path',
                'pdf_original_name',
            ]);
        });

        Schema::table('boletos', function (Blueprint $table): void {
            $table->dropColumn([
                'pdf_path',
                'pdf_original_name',
            ]);
        });
    }
};
