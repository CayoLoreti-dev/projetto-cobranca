<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index('checksum_sha256');
        });

        Schema::table('cobranca_eventos', function (Blueprint $table) {
            $table->index('checksum_sha256');
        });

        Schema::table('parcela_eventos', function (Blueprint $table) {
            $table->index('checksum_sha256');
        });

        Schema::table('boleto_eventos', function (Blueprint $table) {
            $table->index('checksum_sha256');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['checksum_sha256']);
        });

        Schema::table('cobranca_eventos', function (Blueprint $table) {
            $table->dropIndex(['checksum_sha256']);
        });

        Schema::table('parcela_eventos', function (Blueprint $table) {
            $table->dropIndex(['checksum_sha256']);
        });

        Schema::table('boleto_eventos', function (Blueprint $table) {
            $table->dropIndex(['checksum_sha256']);
        });
    }
};
