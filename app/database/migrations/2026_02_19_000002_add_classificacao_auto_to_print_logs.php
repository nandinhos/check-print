<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_logs', function (Blueprint $table) {
            $table->string('classificacao_auto', 20)->after('classificacao')->nullable();
        });

        // Preenche registros existentes: classificacao_auto = classificacao atual
        // (quem nao tem override e AUTO, entao classificacao_auto == classificacao)
        DB::statement("
            UPDATE print_logs
            SET classificacao_auto = classificacao
            WHERE classificacao_origem = 'AUTO'
        ");

        // Para registros manuais, busca a classificacao original no historico de overrides
        DB::statement("
            UPDATE print_logs
            SET classificacao_auto = (
                SELECT classificacao_anterior
                FROM manual_overrides
                WHERE print_log_id = print_logs.id
                ORDER BY id ASC
                LIMIT 1
            )
            WHERE classificacao_origem = 'MANUAL'
              AND EXISTS (
                SELECT 1 FROM manual_overrides
                WHERE print_log_id = print_logs.id
              )
        ");

        // Fallback: qualquer registro sem classificacao_auto ainda recebe o valor atual
        DB::statement("
            UPDATE print_logs
            SET classificacao_auto = classificacao
            WHERE classificacao_auto IS NULL
        ");

        // Torna a coluna obrigatoria apos preencher todos os registros
        Schema::table('print_logs', function (Blueprint $table) {
            $table->string('classificacao_auto', 20)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('print_logs', function (Blueprint $table) {
            $table->dropColumn('classificacao_auto');
        });
    }
};
