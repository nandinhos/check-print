<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_logs', function (Blueprint $table) {
            $table->id();
            $table->string('usuario', 100)->index();
            $table->string('documento', 500);
            $table->dateTime('data_impressao')->index();
            $table->unsignedSmallInteger('paginas')->default(1);
            $table->decimal('custo', 8, 4)->default(0);
            $table->string('aplicativo', 100)->nullable();
            $table->enum('classificacao', ['PESSOAL', 'ADMINISTRATIVO'])->default('ADMINISTRATIVO')->index();
            $table->enum('classificacao_origem', ['AUTO', 'MANUAL'])->default('AUTO');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_logs');
    }
};
