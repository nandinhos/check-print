<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Models\PrintLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardFiltrosTest extends TestCase
{
    use RefreshDatabase;

    public function test_data_inicio_padrao_usa_registro_mais_antigo_do_banco(): void
    {
        PrintLog::factory()->create(['data_impressao' => '2024-03-15 10:00:00']);
        PrintLog::factory()->create(['data_impressao' => '2024-07-20 10:00:00']);

        Livewire::test(Dashboard::class)->assertSet('dataInicio', '2024-03-15');
    }

    public function test_data_inicio_padrao_e_hoje_quando_banco_esta_vazio(): void
    {
        Livewire::test(Dashboard::class)->assertSet('dataInicio', now()->format('Y-m-d'));
    }

    public function test_filtro_usuario_select_filtra_exatamente_pelo_nome(): void
    {
        PrintLog::factory()->create(['usuario' => 'Ana']);
        PrintLog::factory()->create(['usuario' => 'Ana Maria']);
        PrintLog::factory()->create(['usuario' => 'Carlos']);

        Livewire::test(Dashboard::class)
            ->set('filtroUsuario', 'Ana')
            ->assertSet('totalImpressoes', 1);
    }

    public function test_filtro_usuario_todos_quando_vazio(): void
    {
        PrintLog::factory()->count(3)->create();

        Livewire::test(Dashboard::class)
            ->set('filtroUsuario', '')
            ->assertSet('totalImpressoes', 3);
    }

    public function test_usuarios_carregados_no_mount(): void
    {
        PrintLog::factory()->create(['usuario' => 'Alice']);
        PrintLog::factory()->create(['usuario' => 'Bob']);
        PrintLog::factory()->create(['usuario' => 'Alice']); // duplicata

        $component = Livewire::test(Dashboard::class);

        $usuarios = $component->get('usuarios');
        $this->assertContains('Alice', $usuarios);
        $this->assertContains('Bob', $usuarios);
        $this->assertCount(2, $usuarios);
    }
}
