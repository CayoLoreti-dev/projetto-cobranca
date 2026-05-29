<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cobrancas', function (Blueprint $table) {
            // Composite indexes to support WHERE ... ORDER BY created_at
            $table->index(['status', 'created_at'], 'cobrancas_status_created_at_idx');
            $table->index(['cliente_id', 'created_at'], 'cobrancas_cliente_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('cobrancas', function (Blueprint $table) {
            $table->dropIndex('cobrancas_status_created_at_idx');
            $table->dropIndex('cobrancas_cliente_created_at_idx');
        });
    }
};
