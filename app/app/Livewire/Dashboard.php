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

    // Selecao em massa
    public array $selecionados = [];
    public bool  $todosSelecionados = false;

    // Modal de edicao de classificacao
    public bool $modalAberto = false;
    public ?int $modalPrintLogId = null;
    public string $modalDocumento = '';
    public string $modalUsuario = '';
    public string $modalClassificacaoAtual = '';

    // Usuarios para select
    public array $usuarios = [];

    public function mount(): void
    {
        $oldest = PrintLog::min('data_impressao');
        $this->dataInicio = $oldest
            ? \Carbon\Carbon::parse($oldest)->format('Y-m-d')
            : now()->format('Y-m-d');
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

    public function abrirModalEdicao(int $id): void
    {
        $log = PrintLog::findOrFail($id);
        $this->modalPrintLogId = $log->id;
        $this->modalDocumento = $log->documento;
        $this->modalUsuario = $log->usuario;
        $this->modalClassificacaoAtual = $log->classificacao;
        $this->modalAberto = true;
    }

    public function salvarClassificacao(string $novaClassificacao): void
    {
        $log = PrintLog::findOrFail($this->modalPrintLogId);

        if ($novaClassificacao !== $log->classificacao) {
            $anterior = $log->classificacao;

            $log->update([
                'classificacao' => $novaClassificacao,
                'classificacao_origem' => 'MANUAL',
            ]);

            ManualOverride::create([
                'print_log_id'           => $log->id,
                'classificacao_anterior' => $anterior,
                'classificacao_nova'     => $novaClassificacao,
                'alterado_por'           => 'operador',
            ]);

            $this->calcularKpis();
        }

        $this->fecharModal();
    }

    public function reverterClassificacao(int $id): void
    {
        $log = PrintLog::findOrFail($id);

        if (! $log->isManual()) {
            return;
        }

        $anterior = $log->classificacao;

        $log->update([
            'classificacao'        => $log->classificacao_auto,
            'classificacao_origem' => 'AUTO',
        ]);

        ManualOverride::create([
            'print_log_id'           => $log->id,
            'classificacao_anterior' => $anterior,
            'classificacao_nova'     => $log->classificacao_auto,
            'alterado_por'           => 'operador',
        ]);

        $this->calcularKpis();
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->modalPrintLogId = null;
        $this->modalDocumento = '';
        $this->modalUsuario = '';
        $this->modalClassificacaoAtual = '';
    }

    public function toggleTodos(array $idsNaPagina): void
    {
        if ($this->todosSelecionados) {
            $this->selecionados    = [];
            $this->todosSelecionados = false;
        } else {
            $this->selecionados    = $idsNaPagina;
            $this->todosSelecionados = true;
        }
    }

    public function salvarClassificacaoEmMassa(array $ids, string $novaClassificacao): void
    {
        if (empty($ids)) {
            return;
        }

        $logs = PrintLog::whereIn('id', $ids)->get();

        foreach ($logs as $log) {
            if ($novaClassificacao === $log->classificacao) {
                continue;
            }

            $anterior = $log->classificacao;

            $log->update([
                'classificacao'        => $novaClassificacao,
                'classificacao_origem' => 'MANUAL',
            ]);

            ManualOverride::create([
                'print_log_id'           => $log->id,
                'classificacao_anterior' => $anterior,
                'classificacao_nova'     => $novaClassificacao,
                'alterado_por'           => 'operador',
            ]);
        }

        $this->selecionados    = [];
        $this->todosSelecionados = false;
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
            $query->where('usuario', $this->filtroUsuario);
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
            $query->where('usuario', $this->filtroUsuario);
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
