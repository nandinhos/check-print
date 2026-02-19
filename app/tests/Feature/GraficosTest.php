<?php

namespace Tests\Feature;

use App\Livewire\Graficos;
use App\Models\PrintLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GraficosTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_graficos_retorna_ok(): void
    {
        $this->get(route('graficos'))->assertOk();
    }

    public function test_componente_graficos_carrega_sem_dados(): void
    {
        Livewire::test(Graficos::class)->assertStatus(200);
    }

    public function test_dados_administrativos_agrupam_paginas_por_mes(): void
    {
        $ano = now()->year;

        PrintLog::factory()->create([
            'classificacao'      => 'ADMINISTRATIVO',
            'classificacao_auto' => 'ADMINISTRATIVO',
            'data_impressao'     => "{$ano}-01-15 10:00:00",
            'paginas'            => 50,
        ]);
        PrintLog::factory()->create([
            'classificacao'      => 'ADMINISTRATIVO',
            'classificacao_auto' => 'ADMINISTRATIVO',
            'data_impressao'     => "{$ano}-01-20 10:00:00",
            'paginas'            => 30,
        ]);
        PrintLog::factory()->create([
            'classificacao'      => 'ADMINISTRATIVO',
            'classificacao_auto' => 'ADMINISTRATIVO',
            'data_impressao'     => "{$ano}-03-10 10:00:00",
            'paginas'            => 20,
        ]);

        $component = Livewire::test(Graficos::class);
        $dados = $component->instance()->dadosAdministrativo;

        $this->assertEquals(80, $dados['valores'][0]); // Janeiro
        $this->assertEquals(0, $dados['valores'][1]);  // Fevereiro
        $this->assertEquals(20, $dados['valores'][2]); // Marco
    }

    public function test_dados_pessoal_agrupam_paginas_por_mes(): void
    {
        $ano = now()->year;

        PrintLog::factory()->create([
            'classificacao'      => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
            'data_impressao'     => "{$ano}-02-10 10:00:00",
            'paginas'            => 10,
        ]);

        $component = Livewire::test(Graficos::class);
        $dados = $component->instance()->dadosPessoal;

        $this->assertEquals(0, $dados['valores'][0]);  // Janeiro
        $this->assertEquals(10, $dados['valores'][1]); // Fevereiro
    }

    public function test_media_calculada_sobre_12_meses(): void
    {
        $ano = now()->year;

        // Total 120 paginas em janeiro = media 10 por mes (120/12)
        PrintLog::factory()->create([
            'classificacao'      => 'ADMINISTRATIVO',
            'classificacao_auto' => 'ADMINISTRATIVO',
            'data_impressao'     => "{$ano}-01-15 10:00:00",
            'paginas'            => 120,
        ]);

        $component = Livewire::test(Graficos::class);
        $dados = $component->instance()->dadosAdministrativo;

        $this->assertEquals(10.0, $dados['media']); // 120 / 12 = 10
    }

    public function test_media_zero_quando_sem_dados(): void
    {
        $component = Livewire::test(Graficos::class);
        $dados = $component->instance()->dadosAdministrativo;

        $this->assertEquals(0, $dados['media']);
        $this->assertCount(12, $dados['valores']);
        $this->assertEquals(0, array_sum($dados['valores']));
    }

    public function test_mudar_ano_atualiza_dados(): void
    {
        PrintLog::factory()->create([
            'classificacao'      => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
            'data_impressao'     => '2025-06-10 10:00:00',
            'paginas'            => 99,
        ]);

        $component = Livewire::test(Graficos::class)->set('ano', 2025);
        $dados = $component->instance()->dadosPessoal;

        $this->assertEquals(99, $dados['valores'][5]); // Junho = index 5
    }

    public function test_dados_de_outro_ano_nao_aparecem_no_grafico(): void
    {
        $ano = now()->year;

        PrintLog::factory()->create([
            'classificacao'      => 'ADMINISTRATIVO',
            'classificacao_auto' => 'ADMINISTRATIVO',
            'data_impressao'     => ($ano - 1) . '-06-10 10:00:00',
            'paginas'            => 99,
        ]);

        $component = Livewire::test(Graficos::class);
        $dados = $component->instance()->dadosAdministrativo;

        $this->assertEquals(0, array_sum($dados['valores']));
    }

    public function test_classificacao_pessoal_nao_aparece_no_grafico_administrativo(): void
    {
        $ano = now()->year;

        PrintLog::factory()->create([
            'classificacao'      => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
            'data_impressao'     => "{$ano}-01-10 10:00:00",
            'paginas'            => 50,
        ]);

        $component = Livewire::test(Graficos::class);
        $dados = $component->instance()->dadosAdministrativo;

        $this->assertEquals(0, array_sum($dados['valores']));
    }

    public function test_estrutura_dos_dados_contem_campos_obrigatorios(): void
    {
        $component = Livewire::test(Graficos::class);
        $dadosAdm = $component->instance()->dadosAdministrativo;
        $dadosPes = $component->instance()->dadosPessoal;

        foreach ([$dadosAdm, $dadosPes] as $dados) {
            $this->assertArrayHasKey('labels', $dados);
            $this->assertArrayHasKey('valores', $dados);
            $this->assertArrayHasKey('media', $dados);
            $this->assertCount(12, $dados['labels']);
            $this->assertCount(12, $dados['valores']);
        }
    }
}
