<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_log_id')
                ->constrained('print_logs')
                ->cascadeOnDelete();
            $table->enum('classificacao_anterior', ['PESSOAL', 'ADMINISTRATIVO']);
            $table->enum('classificacao_nova', ['PESSOAL', 'ADMINISTRATIVO']);
            $table->string('alterado_por', 100)->default('operador');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_overrides');
    }
};
