<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove duplicatas existentes antes de criar o indice, mantendo o registro mais antigo
        // Sintaxe compativel com MySQL e SQLite
        DB::statement('
            DELETE FROM print_logs
            WHERE id NOT IN (
                SELECT min_id FROM (
                    SELECT MIN(id) as min_id
                    FROM print_logs
                    GROUP BY usuario, data_impressao, paginas
                ) AS keep
            )
        ');

        Schema::table('print_logs', function (Blueprint $table) {
            // Chave de negocio: mesmo usuario + data_impressao + paginas = duplicata
            // documento nao entra no indice (max 500 chars, ultrapassa limite UTF8MB4)
            // — a verificacao por documento e feita em PHP pelo DuplicataService
            $table->unique(
                ['usuario', 'data_impressao', 'paginas'],
                'uq_print_log_registro'
            );
        });
    }

    public function down(): void
    {
        Schema::table('print_logs', function (Blueprint $table) {
            $table->dropUnique('uq_print_log_registro');
        });
    }
};
