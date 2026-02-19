<?php

namespace Tests\Feature;

use App\Models\PrintLog;
use App\Services\DuplicataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportDuplicataTest extends TestCase
{
    use RefreshDatabase;

    private function makeRow(array $overrides = []): array
    {
        return array_merge([
            'usuario'        => 'Ten Franco',
            'documento'      => 'Boleto Nubank',
            'data_impressao' => '2025-08-04 14:15:00',
            'paginas'        => 1,
            'custo'          => 0.02,
            'aplicativo'     => 'Chrome',
            '_valido'        => true,
            '_linha'         => 2,
            '_erros'         => [],
        ], $overrides);
    }

    private function criarRegistro(array $overrides = []): PrintLog
    {
        return PrintLog::create(array_merge([
            'usuario'              => 'Ten Franco',
            'documento'            => 'Boleto Nubank',
            'data_impressao'       => '2025-08-04 14:15:00',
            'paginas'              => 1,
            'custo'                => 0.02,
            'aplicativo'           => 'Chrome',
            'classificacao'        => 'PESSOAL',
            'classificacao_origem' => 'AUTO',
        ], $overrides));
    }

    /** @test */
    public function detecta_duplicata_exata(): void
    {
        $this->criarRegistro();

        $service = new DuplicataService();
        $row     = $this->makeRow();

        $this->assertTrue($service->isDuplicata($row));
    }

    /** @test */
    public function nao_e_duplicata_quando_banco_vazio(): void
    {
        $service = new DuplicataService();
        $row     = $this->makeRow();

        $this->assertFalse($service->isDuplicata($row));
    }

    /** @test */
    public function nao_e_duplicata_com_usuario_diferente(): void
    {
        $this->criarRegistro();

        $service = new DuplicataService();
        $row     = $this->makeRow(['usuario' => 'Sgt Alves']);

        $this->assertFalse($service->isDuplicata($row));
    }

    /** @test */
    public function nao_e_duplicata_com_documento_diferente(): void
    {
        $this->criarRegistro();

        $service = new DuplicataService();
        $row     = $this->makeRow(['documento' => 'Relatorio Mensal']);

        $this->assertFalse($service->isDuplicata($row));
    }

    /** @test */
    public function nao_e_duplicata_com_data_diferente(): void
    {
        $this->criarRegistro();

        $service = new DuplicataService();
        $row     = $this->makeRow(['data_impressao' => '2025-08-05 14:15:00']);

        $this->assertFalse($service->isDuplicata($row));
    }

    /** @test */
    public function nao_e_duplicata_com_paginas_diferente(): void
    {
        $this->criarRegistro();

        $service = new DuplicataService();
        $row     = $this->makeRow(['paginas' => 2]);

        $this->assertFalse($service->isDuplicata($row));
    }

    /** @test */
    public function verifica_lote_retorna_duplicatas_com_linha(): void
    {
        $this->criarRegistro();

        $service = new DuplicataService();
        $rows    = [
            $this->makeRow(['_linha' => 2]),                            // duplicata
            $this->makeRow(['documento' => 'Oficio 001', '_linha' => 3]), // novo
            $this->makeRow(['_linha' => 4]),                            // duplicata
        ];

        $resultado = $service->verificarLote($rows);

        $this->assertCount(2, $resultado['duplicatas']);
        $this->assertCount(1, $resultado['novos']);
        $this->assertEquals(2, $resultado['duplicatas'][0]['linha']);
        $this->assertEquals(4, $resultado['duplicatas'][1]['linha']);
    }

    /** @test */
    public function verifica_lote_detecta_duplicatas_internas_no_proprio_csv(): void
    {
        // Banco vazio — duplicata entre linhas do proprio arquivo
        $service = new DuplicataService();
        $rows    = [
            $this->makeRow(['_linha' => 2]),
            $this->makeRow(['_linha' => 3]), // mesmo que linha 2
        ];

        $resultado = $service->verificarLote($rows);

        $this->assertCount(1, $resultado['duplicatas']);
        $this->assertCount(1, $resultado['novos']);
        $this->assertEquals(3, $resultado['duplicatas'][0]['linha']);
    }

    /** @test */
    public function importacao_pula_duplicatas_e_reporta_contagem(): void
    {
        $this->criarRegistro();

        $totalAntes = PrintLog::count();

        $service = new DuplicataService();
        $rows    = [
            $this->makeRow(['_linha' => 2]),                              // duplicata
            $this->makeRow(['documento' => 'Oficio 002', '_linha' => 3]), // novo
        ];

        $resultado = $service->verificarLote($rows);

        $this->assertEquals(1, $totalAntes);
        $this->assertCount(1, $resultado['novos']);
        $this->assertCount(1, $resultado['duplicatas']);
    }

    /** @test */
    public function indice_unico_impede_insercao_duplicada_no_banco(): void
    {
        $this->criarRegistro();

        $this->expectException(\Illuminate\Database\QueryException::class);

        $this->criarRegistro(); // mesmos dados — deve lancar excecao de unique constraint
    }
}
