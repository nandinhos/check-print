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
            $table->dropUnique('uq_print_log_registro');
        });

        // MySQL: usa prefixo em documento(150) para respeitar o limite de 767 bytes (utf8mb4)
        // SQLite: suporta colunas completas no indice unico sem restricao de tamanho
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

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE print_logs DROP INDEX `uq_print_log_registro`');
        } else {
            Schema::table('print_logs', function (Blueprint $table) {
                $table->dropUnique('uq_print_log_registro');
            });
        }

        Schema::table('print_logs', function (Blueprint $table) {
            $table->unique(
                ['usuario', 'data_impressao', 'paginas'],
                'uq_print_log_registro'
            );
        });
    }
};
