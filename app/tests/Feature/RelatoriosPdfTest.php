<?php

namespace Tests\Feature;

use App\Models\PrintLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelatoriosPdfTest extends TestCase
{
    use RefreshDatabase;

    /** Replica a query $ranking do ExportController para testes diretos. */
    private function queryRanking(?string $dataInicio = null, ?string $dataFim = null, string $filtroUsuario = ''): \Illuminate\Support\Collection
    {
        return PrintLog::query()
            ->when($dataInicio, fn ($q) => $q->whereDate('data_impressao', '>=', $dataInicio))
            ->when($dataFim,    fn ($q) => $q->whereDate('data_impressao', '<=', $dataFim))
            ->when($filtroUsuario, fn ($q) => $q->where('usuario', 'like', '%' . $filtroUsuario . '%'))
            ->selectRaw('
                usuario,
                SUM(CASE WHEN classificacao = "PESSOAL"        THEN 1       ELSE 0 END) as qtd_pessoal,
                SUM(CASE WHEN classificacao = "PESSOAL"        THEN paginas ELSE 0 END) as paginas_pessoal,
                SUM(CASE WHEN classificacao = "PESSOAL"        THEN custo   ELSE 0 END) as custo_pessoal,
                SUM(CASE WHEN classificacao = "ADMINISTRATIVO" THEN 1       ELSE 0 END) as qtd_admin,
                SUM(CASE WHEN classificacao = "ADMINISTRATIVO" THEN paginas ELSE 0 END) as paginas_admin,
                SUM(CASE WHEN classificacao = "ADMINISTRATIVO" THEN custo   ELSE 0 END) as custo_admin,
                SUM(paginas) as total_paginas
            ')
            ->groupBy('usuario')
            ->orderByDesc('total_paginas')
            ->get();
    }

    /** Replica a query $analitico do ExportController para testes diretos. */
    private function queryAnalitico(?string $dataInicio = null, ?string $dataFim = null, string $filtroUsuario = ''): \Illuminate\Support\Collection
    {
        return PrintLog::query()
            ->when($dataInicio, fn ($q) => $q->whereDate('data_impressao', '>=', $dataInicio))
            ->when($dataFim,    fn ($q) => $q->whereDate('data_impressao', '<=', $dataFim))
            ->when($filtroUsuario, fn ($q) => $q->where('usuario', 'like', '%' . $filtroUsuario . '%'))
            ->orderBy('usuario')
            ->orderByRaw("CASE WHEN classificacao = 'PESSOAL' THEN 0 ELSE 1 END")
            ->orderBy('data_impressao')
            ->get(['usuario', 'documento', 'data_impressao', 'paginas', 'custo', 'classificacao']);
    }

    public function test_pdf_ranking_inclui_todos_os_usuarios(): void
    {
        $usuarios = ['Alice', 'Bob', 'Carlos', 'Diana', 'Eduardo', 'Fernanda'];

        foreach ($usuarios as $usuario) {
            PrintLog::factory()->create([
                'usuario'       => $usuario,
                'classificacao' => 'PESSOAL',
                'paginas'       => 5,
            ]);
        }

        $ranking = $this->queryRanking();

        $this->assertCount(6, $ranking);
    }

    public function test_pdf_ranking_ordenado_por_total_paginas_decrescente(): void
    {
        PrintLog::factory()->create(['usuario' => 'Menor', 'paginas' => 2, 'classificacao' => 'PESSOAL']);
        PrintLog::factory()->create(['usuario' => 'Maior', 'paginas' => 20, 'classificacao' => 'PESSOAL']);
        PrintLog::factory()->create(['usuario' => 'Medio', 'paginas' => 10, 'classificacao' => 'PESSOAL']);

        $ranking = $this->queryRanking();

        $this->assertEquals('Maior', $ranking->first()->usuario);
        $this->assertEquals('Menor', $ranking->last()->usuario);
    }

    public function test_pdf_inclui_secao_analitica_com_registros_individuais(): void
    {
        PrintLog::factory()->count(5)->create(['usuario' => 'Alice', 'classificacao' => 'PESSOAL']);
        PrintLog::factory()->count(3)->create(['usuario' => 'Bob', 'classificacao' => 'ADMINISTRATIVO']);

        $analitico = $this->queryAnalitico();

        $this->assertCount(8, $analitico);
    }

    public function test_pdf_analitico_ordena_pessoal_antes_de_administrativo(): void
    {
        PrintLog::factory()->create([
            'usuario'        => 'Alice',
            'classificacao'  => 'ADMINISTRATIVO',
            'data_impressao' => '2024-06-01 09:00:00',
        ]);
        PrintLog::factory()->create([
            'usuario'        => 'Alice',
            'classificacao'  => 'PESSOAL',
            'data_impressao' => '2024-06-01 10:00:00',
        ]);

        $analitico = $this->queryAnalitico();
        $alice = $analitico->where('usuario', 'Alice')->values();

        $this->assertEquals('PESSOAL', $alice->first()->classificacao);
        $this->assertEquals('ADMINISTRATIVO', $alice->last()->classificacao);
    }
}
