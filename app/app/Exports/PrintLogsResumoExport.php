<?php

namespace App\Exports;

use App\Models\PrintLog;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PrintLogsResumoExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(
        private readonly string $dataInicio,
        private readonly string $dataFim,
        private readonly string $filtroUsuario = '',
        private readonly string $filtroTipo = 'todos',
    ) {}

    public function query(): Builder
    {
        return PrintLog::query()
            ->selectRaw('
                usuario,
                COUNT(*) as total_impressoes,
                SUM(paginas) as total_paginas,
                SUM(custo) as custo_total,
                SUM(CASE WHEN classificacao = "PESSOAL" THEN custo ELSE 0 END) as custo_pessoal,
                SUM(CASE WHEN classificacao = "ADMINISTRATIVO" THEN custo ELSE 0 END) as custo_admin,
                SUM(CASE WHEN classificacao = "PESSOAL" THEN 1 ELSE 0 END) as qtd_pessoal,
                SUM(CASE WHEN classificacao = "ADMINISTRATIVO" THEN 1 ELSE 0 END) as qtd_admin
            ')
            ->when($this->dataInicio, fn ($q) => $q->whereDate('data_impressao', '>=', $this->dataInicio))
            ->when($this->dataFim, fn ($q) => $q->whereDate('data_impressao', '<=', $this->dataFim))
            ->when($this->filtroUsuario, fn ($q) => $q->where('usuario', 'like', '%' . $this->filtroUsuario . '%'))
            ->groupBy('usuario')
            ->orderByDesc('custo_pessoal');
    }

    public function headings(): array
    {
        return [
            'Usuario',
            'Total Impressoes',
            'Total Paginas',
            'Custo Total (R$)',
            'Custo Pessoal (R$)',
            'Custo Admin (R$)',
            'Qtd Pessoal',
            'Qtd Admin',
        ];
    }

    public function map($row): array
    {
        return [
            $row->usuario,
            $row->total_impressoes,
            $row->total_paginas,
            $row->custo_total,
            $row->custo_pessoal,
            $row->custo_admin,
            $row->qtd_pessoal,
            $row->qtd_admin,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }

    public function title(): string
    {
        return 'Resumo por Usuario';
    }
}
