<?php

namespace App\Livewire;

use App\Models\ManualOverride;
use App\Models\PrintLog;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    // Filtros de data
    public string $dataInicio = '';
    public string $dataFim = '';

    // Filtros avancados
    public string $filtroUsuario = '';
    public string $filtroTipo = 'todos'; // todos | PESSOAL | ADMINISTRATIVO
    public string $buscaDocumento = '';

    // KPIs calculados
    public int $totalImpressoes = 0;
    public int $totalPaginas = 0;
    public float $custoTotal = 0;
    public float $custoPessoal = 0;
    public float $custoAdministrativo = 0;
    public float $percentualPessoal = 0;

    // Usuarios para autocomplete
    public array $usuarios = [];
    public array $usuariosSugestoes = [];

    public function mount(): void
    {
        $this->dataInicio = now()->startOfMonth()->format('Y-m-d');
        $this->dataFim = now()->format('Y-m-d');
        $this->carregarUsuarios();
        $this->calcularKpis();
    }

    public function updatedDataInicio(): void
    {
        $this->resetPage();
        $this->calcularKpis();
    }

    public function updatedDataFim(): void
    {
        $this->resetPage();
        $this->calcularKpis();
    }

    public function updatedFiltroUsuario(): void
    {
        $this->resetPage();
        $this->calcularKpis();
        $this->buscarSugestoesUsuario();
    }

    public function updatedFiltroTipo(): void
    {
        $this->resetPage();
        $this->calcularKpis();
    }

    public function updatedBuscaDocumento(): void
    {
        $this->resetPage();
    }

    public function setPreset(string $preset): void
    {
        match ($preset) {
            'ultimos30' => [
                $this->dataInicio = now()->subDays(30)->format('Y-m-d'),
                $this->dataFim    = now()->format('Y-m-d'),
            ],
            'esteMes' => [
                $this->dataInicio = now()->startOfMonth()->format('Y-m-d'),
                $this->dataFim    = now()->endOfMonth()->format('Y-m-d'),
            ],
            'anoAtual' => [
                $this->dataInicio = now()->startOfYear()->format('Y-m-d'),
                $this->dataFim    = now()->endOfYear()->format('Y-m-d'),
            ],
            default => null,
        };

        $this->resetPage();
        $this->calcularKpis();
    }

    public function alternarClassificacao(int $id): void
    {
        $log = PrintLog::findOrFail($id);
        $anterior = $log->classificacao;
        $nova = $anterior === 'PESSOAL' ? 'ADMINISTRATIVO' : 'PESSOAL';

        $log->update([
            'classificacao' => $nova,
            'classificacao_origem' => 'MANUAL',
        ]);

        ManualOverride::create([
            'print_log_id'          => $log->id,
            'classificacao_anterior' => $anterior,
            'classificacao_nova'     => $nova,
            'alterado_por'          => 'operador',
        ]);

        $this->calcularKpis();
    }

    #[On('csv-importado')]
    public function onCsvImportado(): void
    {
        $this->carregarUsuarios();
        $this->calcularKpis();
    }

    private function baseQuery(): Builder
    {
        $query = PrintLog::query();

        if ($this->dataInicio) {
            $query->whereDate('data_impressao', '>=', $this->dataInicio);
        }

        if ($this->dataFim) {
            $query->whereDate('data_impressao', '<=', $this->dataFim);
        }

        if ($this->filtroUsuario) {
            $query->where('usuario', 'like', '%' . $this->filtroUsuario . '%');
        }

        if ($this->filtroTipo !== 'todos') {
            $query->where('classificacao', $this->filtroTipo);
        }

        if ($this->buscaDocumento) {
            $query->where('documento', 'like', '%' . $this->buscaDocumento . '%');
        }

        return $query;
    }

    private function calcularKpis(): void
    {
        $query = PrintLog::query();

        if ($this->dataInicio) {
            $query->whereDate('data_impressao', '>=', $this->dataInicio);
        }
        if ($this->dataFim) {
            $query->whereDate('data_impressao', '<=', $this->dataFim);
        }
        if ($this->filtroUsuario) {
            $query->where('usuario', 'like', '%' . $this->filtroUsuario . '%');
        }

        $totais = (clone $query)->selectRaw('
            COUNT(*) as total_impressoes,
            SUM(paginas) as total_paginas,
            SUM(custo) as custo_total,
            SUM(CASE WHEN classificacao = "PESSOAL" THEN custo ELSE 0 END) as custo_pessoal,
            SUM(CASE WHEN classificacao = "ADMINISTRATIVO" THEN custo ELSE 0 END) as custo_admin
        ')->first();

        $this->totalImpressoes    = (int) ($totais->total_impressoes ?? 0);
        $this->totalPaginas       = (int) ($totais->total_paginas ?? 0);
        $this->custoTotal         = (float) ($totais->custo_total ?? 0);
        $this->custoPessoal       = (float) ($totais->custo_pessoal ?? 0);
        $this->custoAdministrativo = (float) ($totais->custo_admin ?? 0);
        $this->percentualPessoal  = $this->custoTotal > 0
            ? round(($this->custoPessoal / $this->custoTotal) * 100, 1)
            : 0;
    }

    private function carregarUsuarios(): void
    {
        $this->usuarios = PrintLog::distinct()
            ->orderBy('usuario')
            ->pluck('usuario')
            ->toArray();
    }

    private function buscarSugestoesUsuario(): void
    {
        if (strlen($this->filtroUsuario) < 2) {
            $this->usuariosSugestoes = [];
            return;
        }

        $this->usuariosSugestoes = PrintLog::where('usuario', 'like', '%' . $this->filtroUsuario . '%')
            ->distinct()
            ->orderBy('usuario')
            ->limit(10)
            ->pluck('usuario')
            ->toArray();
    }

    public function render()
    {
        $logs = $this->baseQuery()
            ->orderBy('data_impressao', 'desc')
            ->paginate(15);

        return view('livewire.dashboard', [
            'logs' => $logs,
        ]);
    }
}
