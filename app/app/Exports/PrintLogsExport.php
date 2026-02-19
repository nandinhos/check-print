<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PrintLogsExport implements WithMultipleSheets
{
    public function __construct(
        private readonly string $dataInicio,
        private readonly string $dataFim,
        private readonly string $filtroUsuario = '',
        private readonly string $filtroTipo = 'todos',
        private readonly string $buscaDocumento = '',
    ) {}

    public function sheets(): array
    {
        return [
            new PrintLogsResumoExport(
                $this->dataInicio,
                $this->dataFim,
                $this->filtroUsuario,
                $this->filtroTipo,
            ),
            new PrintLogsDetalhadoExport(
                $this->dataInicio,
                $this->dataFim,
                $this->filtroUsuario,
                $this->filtroTipo,
                $this->buscaDocumento,
            ),
        ];
    }
}
