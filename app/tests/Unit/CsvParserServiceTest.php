<?php

namespace Tests\Unit;

use App\Services\CsvParserService;
use PHPUnit\Framework\TestCase;

class CsvParserServiceTest extends TestCase
{
    private CsvParserService $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new CsvParserService();
    }

    public function test_parse_csv_com_ponto_e_virgula(): void
    {
        $csv = "Data;Hora;Usuario;Documento;Paginas;Custo;Aplicativo\n27/02/2025;13:32:24;1S Brasil;Ficha S1 Caetano;1;0.02;PDF";

        $rows = $this->parser->parse($csv);

        $this->assertCount(1, $rows);
        $this->assertEquals('1S Brasil', $rows[0]['usuario']);
        $this->assertEquals('Ficha S1 Caetano', $rows[0]['documento']);
        $this->assertEquals(1, $rows[0]['paginas']);
        $this->assertEquals(0.02, $rows[0]['custo']);
    }

    public function test_parse_csv_com_virgula_como_separador(): void
    {
        $csv = "Data,Hora,Usuario,Documento,Paginas,Custo,Aplicativo\n04/08/2025,14:15:00,Ten Franco,Boleto Nubank,1,0.02,Chrome";

        $rows = $this->parser->parse($csv);

        $this->assertCount(1, $rows);
        $this->assertEquals('Ten Franco', $rows[0]['usuario']);
        $this->assertEquals('Boleto Nubank', $rows[0]['documento']);
    }

    public function test_parse_data_no_formato_brasileiro(): void
    {
        $csv = "Data;Hora;Usuario;Documento;Paginas;Custo;Aplicativo\n27/02/2025;13:32:24;1S Brasil;Ficha;1;0.02;PDF";

        $rows = $this->parser->parse($csv);

        $this->assertEquals('2025-02-27 13:32:24', $rows[0]['data_impressao']);
    }

    public function test_parse_custo_com_virgula_decimal(): void
    {
        $csv = "Data;Hora;Usuario;Documento;Paginas;Custo;Aplicativo\n27/02/2025;13:32:24;Usuario;Documento;1;1,50;PDF";

        $rows = $this->parser->parse($csv);

        $this->assertEquals(1.50, $rows[0]['custo']);
    }

    public function test_valida_cabecalho_com_colunas_minimas(): void
    {
        $csv = "Data;Hora;Usuario;Documento;Paginas;Custo;Aplicativo\n27/02/2025;13:32:24;U;D;1;0.02;PDF";

        $this->assertTrue($this->parser->validateHeader($csv));
    }

    public function test_rejeita_cabecalho_sem_colunas_obrigatorias(): void
    {
        $csv = "Nome;Tipo\nFoo;Bar";

        $this->assertFalse($this->parser->validateHeader($csv));
    }

    public function test_parse_multiplas_linhas(): void
    {
        $csv = "Data;Hora;Usuario;Documento;Paginas;Custo;Aplicativo\n" .
               "27/02/2025;13:32:24;User A;Doc A;1;0.02;PDF\n" .
               "28/02/2025;14:00:00;User B;Doc B;5;0.10;Chrome";

        $rows = $this->parser->parse($csv);

        $this->assertCount(2, $rows);
        $this->assertEquals('User A', $rows[0]['usuario']);
        $this->assertEquals('User B', $rows[1]['usuario']);
    }

    public function test_ignora_linhas_vazias(): void
    {
        $csv = "Data;Hora;Usuario;Documento;Paginas;Custo;Aplicativo\n" .
               "27/02/2025;13:32:24;User A;Doc A;1;0.02;PDF\n\n";

        $rows = $this->parser->parse($csv);

        $this->assertCount(1, $rows);
    }
}
