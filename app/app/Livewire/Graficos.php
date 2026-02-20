<?php

namespace App\Livewire;

use App\Models\PrintLog;
use Illuminate\View\View;
use Livewire\Component;

class Graficos extends Component
{
    public int $ano;

    public array $dadosAdministrativo = [];

    public array $dadosPessoal = [];

    public function mount(): void
    {
        $ultimoLog = \App\Models\PrintLog::latest('data_impressao')->first(); $this->ano = $ultimoLog ? (int) $ultimoLog->data_impressao->format('Y') : (int) now()->format('Y');
        $this->carregarDados();
    }

    public function updatedAno(): void
    {
        $this->carregarDados();
        $this->dispatch('graficos-atualizados',
            admin:   $this->dadosAdministrativo,
            pessoal: $this->dadosPessoal,
        );
    }

    private function carregarDados(): void
    {
        $this->dadosAdministrativo = $this->calcularDadosPorClassificacao('ADMINISTRATIVO');
        $this->dadosPessoal        = $this->calcularDadosPorClassificacao('PESSOAL');
    }

    private function calcularDadosPorClassificacao(string $classificacao): array
    {
        $porMes = PrintLog::query()
            ->selectRaw('MONTH(data_impressao) as mes, SUM(paginas) as total')
            ->whereYear('data_impressao', $this->ano)
            ->where('classificacao', $classificacao)
            ->groupBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        $valores = [];
        for ($m = 1; $m <= 12; $m++) {
            $valores[] = (int) ($porMes[$m] ?? 0);
        }

        $totalAnual = array_sum($valores);
        $media      = $totalAnual > 0 ? round($totalAnual / 12, 1) : 0;

        return [
            'labels'  => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            'valores' => $valores,
            'media'   => $media,
        ];
    }

    public function anosDisponiveis(): array
    {
        $anos = PrintLog::query()
            ->selectRaw('YEAR(data_impressao) as ano')
            ->distinct()
            ->orderBy('ano', 'desc')
            ->pluck('ano')
            ->toArray();

        if (empty($anos)) {
            return [(int) now()->format('Y')];
        }

        return $anos;
    }

    public function render(): View
    {
        return view('livewire.graficos', [
            'anosDisponiveis' => $this->anosDisponiveis(),
        ]);
    }
}
