<?php

namespace Tests\Feature;

use App\Models\ManualOverride;
use App\Models\PrintLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSelecaoMassaTest extends TestCase
{
    use RefreshDatabase;

    private function criarLog(array $overrides = []): PrintLog
    {
        return PrintLog::create(array_merge([
            'usuario'              => 'Ten Franco',
            'documento'            => 'Boleto Nubank',
            'data_impressao'       => '2025-08-04 14:15:00',
            'paginas'              => 1,
            'custo'                => 0.02,
            'aplicativo'           => 'Chrome',
            'classificacao'        => 'ADMINISTRATIVO',
            'classificacao_auto'   => 'ADMINISTRATIVO',
            'classificacao_origem' => 'AUTO',
        ], $overrides));
    }

    /** @test */
    public function salvar_em_massa_altera_classificacao_dos_selecionados(): void
    {
        $log1 = $this->criarLog(['documento' => 'Doc A']);
        $log2 = $this->criarLog(['documento' => 'Doc B']);
        $log3 = $this->criarLog(['documento' => 'Doc C']);

        $component = \Livewire\Livewire::test(\App\Livewire\Dashboard::class);

        $component->call('salvarClassificacaoEmMassa', [$log1->id, $log2->id], 'PESSOAL');

        $this->assertSame('PESSOAL', $log1->fresh()->classificacao);
        $this->assertSame('PESSOAL', $log2->fresh()->classificacao);
        $this->assertSame('ADMINISTRATIVO', $log3->fresh()->classificacao); // nao alterado
    }

    /** @test */
    public function salvar_em_massa_marca_origem_como_manual(): void
    {
        $log = $this->criarLog();

        $component = \Livewire\Livewire::test(\App\Livewire\Dashboard::class);
        $component->call('salvarClassificacaoEmMassa', [$log->id], 'PESSOAL');

        $this->assertSame('MANUAL', $log->fresh()->classificacao_origem);
    }

    /** @test */
    public function salvar_em_massa_cria_manual_override_para_cada_registro_alterado(): void
    {
        $log1 = $this->criarLog(['documento' => 'Doc A', 'classificacao' => 'ADMINISTRATIVO']);
        $log2 = $this->criarLog(['documento' => 'Doc B', 'classificacao' => 'PESSOAL']); // ja e PESSOAL

        $component = \Livewire\Livewire::test(\App\Livewire\Dashboard::class);
        $component->call('salvarClassificacaoEmMassa', [$log1->id, $log2->id], 'PESSOAL');

        // Apenas log1 muda (log2 ja era PESSOAL)
        $this->assertSame(1, ManualOverride::count());
        $override = ManualOverride::first();
        $this->assertSame($log1->id, $override->print_log_id);
        $this->assertSame('ADMINISTRATIVO', $override->classificacao_anterior);
        $this->assertSame('PESSOAL', $override->classificacao_nova);
    }

    /** @test */
    public function salvar_em_massa_com_lista_vazia_nao_altera_nada(): void
    {
        $log = $this->criarLog();

        $component = \Livewire\Livewire::test(\App\Livewire\Dashboard::class);
        $component->call('salvarClassificacaoEmMassa', [], 'PESSOAL');

        $this->assertSame('ADMINISTRATIVO', $log->fresh()->classificacao);
        $this->assertSame(0, ManualOverride::count());
    }

    /** @test */
    public function salvar_em_massa_limpa_selecao_apos_salvar(): void
    {
        $log = $this->criarLog();

        $component = \Livewire\Livewire::test(\App\Livewire\Dashboard::class);
        $component
            ->set('selecionados', [$log->id])
            ->call('salvarClassificacaoEmMassa', [$log->id], 'PESSOAL')
            ->assertSet('selecionados', []);
    }
}
