<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove a constraint unique do banco.
     * A deduplicacao e feita em software pelo DuplicataService, que permite
     * ao usuario aprovar explicitamente duplicatas reais (mesma impressao feita
     * duas vezes pelo mesmo usuario).
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE print_logs DROP INDEX `uq_print_log_registro`');
        } else {
            Schema::table('print_logs', function (Blueprint $table) {
                $table->dropUnique('uq_print_log_registro');
            });
        }

        // Mantem indice simples (nao-unico) para performance de busca
        Schema::table('print_logs', function (Blueprint $table) {
            $table->index(['usuario', 'data_impressao', 'paginas'], 'idx_print_log_dedup');
        });
    }

    public function down(): void
    {
        Schema::table('print_logs', function (Blueprint $table) {
            $table->dropIndex('idx_print_log_dedup');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('
                ALTER TABLE print_logs
                ADD UNIQUE KEY `uq_print_log_registro`
                (`usuario`(100), `documento`(150), `data_impressao`, `paginas`)
            ');
        } else {
            Schema::table('print_logs', function (Blueprint $table) {
                $table->unique(
                    ['usuario', 'documento', 'data_impressao', 'paginas'],
                    'uq_print_log_registro'
                );
            });
        }
    }
};
