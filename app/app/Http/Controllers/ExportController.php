<?php

namespace App\Http\Controllers;

use App\Exports\PrintLogsExport;
use App\Models\PrintLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function excel(Request $request): BinaryFileResponse
    {
        $dataInicio     = $request->get('data_inicio') ?? now()->startOfMonth()->format('Y-m-d');
        $dataFim        = $request->get('data_fim') ?? now()->format('Y-m-d');
        $filtroUsuario  = $request->get('usuario') ?? '';
        $filtroTipo     = $request->get('tipo') ?? 'todos';
        $buscaDocumento = $request->get('documento') ?? '';

        $filename = 'auditoria-impressoes-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(
            new PrintLogsExport($dataInicio, $dataFim, $filtroUsuario, $filtroTipo, $buscaDocumento),
            $filename
        );
    }

    public function pdf(Request $request): Response
    {
        // Aumenta os limites para o processamento de grandes volumes
        ini_set('memory_limit', '-1'); // Sem limite para este processo
        set_time_limit(0);             // Sem timeout para este processo

        $dataInicio    = $request->get('data_inicio') ?? now()->startOfMonth()->format('Y-m-d');
        $dataFim       = $request->get('data_fim') ?? now()->format('Y-m-d');
        $filtroUsuario = $request->get('usuario') ?? '';

        $totais = PrintLog::query()
            ->when($dataInicio, fn ($q) => $q->whereDate('data_impressao', '>=', $dataInicio))
            ->when($dataFim, fn ($q) => $q->whereDate('data_impressao', '<=', $dataFim))
            ->when($filtroUsuario, fn ($q) => $q->where('usuario', 'like', '%' . $filtroUsuario . '%'))
            ->selectRaw('
                COUNT(*) as total_impressoes,
                SUM(paginas) as total_paginas,
                SUM(custo) as custo_total,
                SUM(CASE WHEN classificacao = "PESSOAL" THEN custo ELSE 0 END) as custo_pessoal,
                SUM(CASE WHEN classificacao = "ADMINISTRATIVO" THEN custo ELSE 0 END) as custo_admin
            ')
            ->toBase() // Reduz drasticamente o consumo de memoria (evita instanciar models)
            ->first();

        $custoTotal = (float) ($totais->custo_total ?? 0);
        $custoPessoal = (float) ($totais->custo_pessoal ?? 0);

        $kpis = [
            'total_impressoes'   => (int) ($totais->total_impressoes ?? 0),
            'total_paginas'      => (int) ($totais->total_paginas ?? 0),
            'custo_total'        => $custoTotal,
            'custo_pessoal'      => $custoPessoal,
            'custo_admin'        => (float) ($totais->custo_admin ?? 0),
            'percentual_pessoal' => $custoTotal > 0
                ? round(($custoPessoal / $custoTotal) * 100, 1)
                : 0,
        ];

        $ranking = PrintLog::query()
            ->when($dataInicio, fn ($q) => $q->whereDate('data_impressao', '>=', $dataInicio))
            ->when($dataFim,    fn ($q) => $q->whereDate('data_impressao', '<=', $dataFim))
            ->when($filtroUsuario, fn ($q) => $q->where('usuario', 'like', '%' . $filtroUsuario . '%'))
            ->selectRaw('
                usuario,
                SUM(CASE WHEN classificacao = "PESSOAL"        THEN 1       ELSE 0 END) as qtd_pessoal,
                SUM(CASE WHEN classificacao = "PESSOAL"        THEN paginas ELSE 0 END) as paginas_pessoal,
                SUM(CASE WHEN classificacao = "PESSOAL"        THEN custo   ELSE 0 END) as custo_pessoal,
                SUM(CASE WHEN classificacao = "ADMINISTRATIVO" THEN 1       ELSE 0 END) as qtd_admin,
                SUM(CASE WHEN classificacao = "ADMINISTRATIVO" THEN paginas ELSE 0 END) as paginas_admin,
                SUM(CASE WHEN classificacao = "ADMINISTRATIVO" THEN custo   ELSE 0 END) as custo_admin,
                SUM(paginas) as total_paginas
            ')
            ->groupBy('usuario')
            ->orderByDesc('total_paginas')
            ->limit(20)
            ->toBase() // Evita instanciar models
            ->get();

        $analitico = PrintLog::query()
            ->when($dataInicio, fn ($q) => $q->whereDate('data_impressao', '>=', $dataInicio))
            ->when($dataFim,    fn ($q) => $q->whereDate('data_impressao', '<=', $dataFim))
            ->when($filtroUsuario, fn ($q) => $q->where('usuario', 'like', '%' . $filtroUsuario . '%'))
            ->orderBy('usuario')
            ->orderByRaw("CASE WHEN classificacao = 'PESSOAL' THEN 0 ELSE 1 END")
            ->orderBy('data_impressao')
            ->toBase() // Essencial para grandes quantidades: busca apenas dados crus do banco
            ->get(['usuario', 'documento', 'data_impressao', 'paginas', 'custo', 'classificacao']);

        $pdf = Pdf::loadView('reports.executive', [
            'kpis'       => $kpis,
            'ranking'    => $ranking,
            'analitico'  => $analitico,
            'dataInicio' => \Illuminate\Support\Carbon::parse($dataInicio)->format('d/m/Y'),
            'dataFim'    => \Illuminate\Support\Carbon::parse($dataFim)->format('d/m/Y'),
        ]);
        
        // Configuracoes extras para performance e memoria
        $pdf->getDomPDF()->set_option('enable_php', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', false); // Desabilita fonts/imagens remotas para velocidade
        
        $pdf->setPaper('a4');

        $filename = 'relatorio-auditoria-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }

    public function modeloCsv(): BinaryFileResponse
    {
        $path = resource_path('templates/modelo-impressoes.csv');

        return response()->download($path, 'modelo-impressoes-gap.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
