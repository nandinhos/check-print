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

class PrintLogsDetalhadoExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(
        private readonly string $dataInicio,
        private readonly string $dataFim,
        private readonly string $filtroUsuario = '',
        private readonly string $filtroTipo = 'todos',
        private readonly string $buscaDocumento = '',
    ) {}

    public function query(): Builder
    {
        return PrintLog::query()
            ->when($this->dataInicio, fn ($q) => $q->whereDate('data_impressao', '>=', $this->dataInicio))
            ->when($this->dataFim, fn ($q) => $q->whereDate('data_impressao', '<=', $this->dataFim))
            ->when($this->filtroUsuario, fn ($q) => $q->where('usuario', 'like', '%' . $this->filtroUsuario . '%'))
            ->when($this->filtroTipo !== 'todos', fn ($q) => $q->where('classificacao', $this->filtroTipo))
            ->when($this->buscaDocumento, fn ($q) => $q->where('documento', 'like', '%' . $this->buscaDocumento . '%'))
            ->orderBy('data_impressao', 'desc');
    }

    public function headings(): array
    {
        return [
            'Data',
            'Hora',
            'Usuario',
            'Documento',
            'Paginas',
            'Custo (R$)',
            'Aplicativo',
            'Classificacao',
            'Origem',
        ];
    }

    public function map($row): array
    {
        return [
            $row->data_impressao->format('d/m/Y'),
            $row->data_impressao->format('H:i:s'),
            $row->usuario,
            $row->documento,
            $row->paginas,
            $row->custo,
            $row->aplicativo,
            $row->classificacao,
            $row->classificacao_origem,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }

    public function title(): string
    {
        return 'Detalhado';
    }
}
