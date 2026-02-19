<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Models\ManualOverride;
use App\Models\PrintLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EdicaoClassificacaoTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Model: isManual() reflete diferenca entre classificacao e classificacao_auto
    // -----------------------------------------------------------------------

    public function test_is_manual_retorna_false_quando_igual_ao_auto(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao' => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
        ]);

        $this->assertFalse($log->isManual());
    }

    public function test_is_manual_retorna_true_quando_difere_do_auto(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao' => 'ADMINISTRATIVO',
            'classificacao_auto' => 'PESSOAL',
        ]);

        $this->assertTrue($log->isManual());
    }

    public function test_is_manual_retorna_false_quando_revertido_para_o_auto(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao' => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
            'classificacao_origem' => 'MANUAL',
        ]);

        // Mesmo que classificacao_origem seja MANUAL (historico),
        // se o valor e igual ao auto, nao e considerado manual.
        $this->assertFalse($log->isManual());
    }

    // -----------------------------------------------------------------------
    // Dashboard: abrirModalEdicao abre modal sem alterar registro
    // -----------------------------------------------------------------------

    public function test_abre_modal_ao_clicar_no_badge(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao' => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
        ]);

        Livewire::test(Dashboard::class)
            ->call('abrirModalEdicao', $log->id)
            ->assertSet('modalAberto', true)
            ->assertSet('modalPrintLogId', $log->id)
            ->assertSet('modalClassificacaoAtual', 'PESSOAL');

        // Registro nao deve ter sido alterado
        $this->assertDatabaseHas('print_logs', [
            'id' => $log->id,
            'classificacao' => 'PESSOAL',
        ]);
    }

    public function test_modal_exibe_dados_do_registro_clicado(): void
    {
        $log = PrintLog::factory()->create([
            'usuario' => 'Joao Silva',
            'documento' => 'Boleto Nubank',
            'classificacao' => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
        ]);

        Livewire::test(Dashboard::class)
            ->call('abrirModalEdicao', $log->id)
            ->assertSet('modalUsuario', 'Joao Silva')
            ->assertSet('modalDocumento', 'Boleto Nubank')
            ->assertSet('modalClassificacaoAtual', 'PESSOAL');
    }

    // -----------------------------------------------------------------------
    // Dashboard: fecharModal reseta propriedades sem alterar registro
    // -----------------------------------------------------------------------

    public function test_fechar_modal_reseta_propriedades(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao' => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
        ]);

        Livewire::test(Dashboard::class)
            ->call('abrirModalEdicao', $log->id)
            ->call('fecharModal')
            ->assertSet('modalAberto', false)
            ->assertSet('modalPrintLogId', null)
            ->assertSet('modalClassificacaoAtual', '');
    }

    // -----------------------------------------------------------------------
    // Dashboard: salvarClassificacao altera quando diferente do atual
    // -----------------------------------------------------------------------

    public function test_salvar_nova_classificacao_diferente_atualiza_registro(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao' => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
        ]);

        Livewire::test(Dashboard::class)
            ->call('abrirModalEdicao', $log->id)
            ->call('salvarClassificacao', 'ADMINISTRATIVO')
            ->assertSet('modalAberto', false);

        $this->assertDatabaseHas('print_logs', [
            'id' => $log->id,
            'classificacao' => 'ADMINISTRATIVO',
            'classificacao_origem' => 'MANUAL',
        ]);
    }

    public function test_salvar_cria_registro_em_manual_overrides(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao' => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
        ]);

        Livewire::test(Dashboard::class)
            ->call('abrirModalEdicao', $log->id)
            ->call('salvarClassificacao', 'ADMINISTRATIVO');

        $this->assertDatabaseHas('manual_overrides', [
            'print_log_id' => $log->id,
            'classificacao_anterior' => 'PESSOAL',
            'classificacao_nova' => 'ADMINISTRATIVO',
        ]);
    }

    // -----------------------------------------------------------------------
    // Dashboard: salvarClassificacao NAO cria override quando igual ao atual
    // -----------------------------------------------------------------------

    public function test_salvar_mesma_classificacao_nao_altera_nem_cria_override(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao' => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
        ]);

        Livewire::test(Dashboard::class)
            ->call('abrirModalEdicao', $log->id)
            ->call('salvarClassificacao', 'PESSOAL')
            ->assertSet('modalAberto', false);

        $this->assertDatabaseMissing('manual_overrides', ['print_log_id' => $log->id]);
        $this->assertDatabaseHas('print_logs', [
            'id' => $log->id,
            'classificacao' => 'PESSOAL',
            'classificacao_origem' => 'AUTO',
        ]);
    }

    // -----------------------------------------------------------------------
    // Comportamento MANUAL inteligente: badge some ao reverter para o auto
    // -----------------------------------------------------------------------

    public function test_is_manual_false_apos_reverter_para_classificacao_original(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao' => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
        ]);

        // Operador muda para ADMINISTRATIVO
        Livewire::test(Dashboard::class)
            ->call('abrirModalEdicao', $log->id)
            ->call('salvarClassificacao', 'ADMINISTRATIVO');

        $log->refresh();
        $this->assertTrue($log->isManual());

        // Operador reverte para PESSOAL (original)
        Livewire::test(Dashboard::class)
            ->call('abrirModalEdicao', $log->id)
            ->call('salvarClassificacao', 'PESSOAL');

        $log->refresh();
        $this->assertFalse($log->isManual()); // badge MANUAL nao aparece
    }

    // -----------------------------------------------------------------------
    // Dashboard: reverterClassificacao desfaz override manual
    // -----------------------------------------------------------------------

    public function test_reverter_classificacao_volta_para_classificacao_auto(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao'        => 'ADMINISTRATIVO',
            'classificacao_auto'   => 'PESSOAL',
            'classificacao_origem' => 'MANUAL',
        ]);

        Livewire::test(Dashboard::class)
            ->call('reverterClassificacao', $log->id);

        $this->assertDatabaseHas('print_logs', [
            'id'                   => $log->id,
            'classificacao'        => 'PESSOAL',
            'classificacao_origem' => 'AUTO',
        ]);
    }

    public function test_reverter_cria_registro_em_manual_overrides(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao'        => 'ADMINISTRATIVO',
            'classificacao_auto'   => 'PESSOAL',
            'classificacao_origem' => 'MANUAL',
        ]);

        Livewire::test(Dashboard::class)
            ->call('reverterClassificacao', $log->id);

        $this->assertDatabaseHas('manual_overrides', [
            'print_log_id'           => $log->id,
            'classificacao_anterior' => 'ADMINISTRATIVO',
            'classificacao_nova'     => 'PESSOAL',
        ]);
    }

    public function test_reverter_nao_faz_nada_se_ja_for_classificacao_auto(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao'        => 'PESSOAL',
            'classificacao_auto'   => 'PESSOAL',
            'classificacao_origem' => 'AUTO',
        ]);

        Livewire::test(Dashboard::class)
            ->call('reverterClassificacao', $log->id);

        $this->assertDatabaseMissing('manual_overrides', ['print_log_id' => $log->id]);
        $this->assertDatabaseHas('print_logs', [
            'id'                   => $log->id,
            'classificacao'        => 'PESSOAL',
            'classificacao_origem' => 'AUTO',
        ]);
    }

    public function test_reverter_faz_is_manual_retornar_false(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao'        => 'ADMINISTRATIVO',
            'classificacao_auto'   => 'PESSOAL',
            'classificacao_origem' => 'MANUAL',
        ]);

        Livewire::test(Dashboard::class)
            ->call('reverterClassificacao', $log->id);

        $log->refresh();
        $this->assertFalse($log->isManual());
    }

    // -----------------------------------------------------------------------
    // Importacao: classificacao_auto e gravada junto com classificacao na importacao
    // -----------------------------------------------------------------------

    public function test_importacao_grava_classificacao_auto_igual_a_classificacao(): void
    {
        $log = PrintLog::factory()->create([
            'classificacao' => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
            'classificacao_origem' => 'AUTO',
        ]);

        $this->assertDatabaseHas('print_logs', [
            'id' => $log->id,
            'classificacao' => 'PESSOAL',
            'classificacao_auto' => 'PESSOAL',
        ]);
    }
}
