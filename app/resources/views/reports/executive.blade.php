<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1e293b; background: #fff; }

        .header { padding: 24px 32px; border-bottom: 3px solid #1e3a8a; display: flex; justify-content: space-between; align-items: flex-start; }
        .org-name { font-size: 14px; font-weight: 700; color: #1e3a8a; }
        .report-title { font-size: 12px; font-weight: 600; color: #334155; margin-top: 4px; }
        .report-period { font-size: 9px; color: #64748b; margin-top: 2px; }
        .report-date { font-size: 9px; color: #64748b; text-align: right; }

        .section { padding: 20px 32px; }
        .section-title { font-size: 11px; font-weight: 700; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e2e8f0; }

        .kpi-grid { display: table; width: 100%; }
        .kpi-card { display: table-cell; width: 25%; padding: 12px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; text-align: center; }
        .kpi-card + .kpi-card { margin-left: 12px; }
        .kpi-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        .kpi-value { font-size: 16px; font-weight: 700; color: #1e293b; margin-top: 4px; }
        .kpi-sub { font-size: 8px; color: #94a3b8; margin-top: 2px; }
        .kpi-pessoal { background: #fffbeb; border-color: #fcd34d; }
        .kpi-pessoal .kpi-value { color: #b45309; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f1f5f9; }
        th { padding: 8px 10px; text-align: left; font-size: 9px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #cbd5e1; }
        td { padding: 7px 10px; font-size: 9px; color: #334155; border-bottom: 1px solid #f1f5f9; }
        tr:nth-child(even) td { background: #f8fafc; }

        .badge-pessoal { display: inline-block; padding: 2px 6px; background: #fef3c7; color: #92400e; border-radius: 4px; font-size: 8px; font-weight: 600; }
        .badge-admin { display: inline-block; padding: 2px 6px; background: #ede9fe; color: #5b21b6; border-radius: 4px; font-size: 8px; font-weight: 600; }

        .footer { padding: 16px 32px; border-top: 1px solid #e2e8f0; font-size: 8px; color: #94a3b8; text-align: center; margin-top: 16px; }
    </style>
</head>
<body>

    <!-- Cabecalho -->
    <div class="header">
        <div>
            <div class="org-name">GAC-PAC &mdash; Gabinete de Auditoria Corporativa</div>
            <div class="report-title">Relatorio de Auditoria de Impressao</div>
            <div class="report-period">Periodo: {{ $dataInicio }} a {{ $dataFim }}</div>
        </div>
        <div class="report-date">
            Gerado em: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Sumario Executivo -->
    <div class="section">
        <div class="section-title">Sumario Executivo</div>
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Total Impressoes</div>
                <div class="kpi-value">{{ number_format($kpis['total_impressoes'], 0, ',', '.') }}</div>
                <div class="kpi-sub">{{ number_format($kpis['total_paginas'], 0, ',', '.') }} paginas</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Custo Total</div>
                <div class="kpi-value">R$ {{ number_format($kpis['custo_total'], 2, ',', '.') }}</div>
                <div class="kpi-sub">no periodo</div>
            </div>
            <div class="kpi-card kpi-pessoal">
                <div class="kpi-label">Custo Pessoal</div>
                <div class="kpi-value">R$ {{ number_format($kpis['custo_pessoal'], 2, ',', '.') }}</div>
                <div class="kpi-sub">{{ $kpis['percentual_pessoal'] }}% do total</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Administrativo</div>
                <div class="kpi-value">R$ {{ number_format($kpis['custo_admin'], 2, ',', '.') }}</div>
                <div class="kpi-sub">{{ 100 - $kpis['percentual_pessoal'] }}% do total</div>
            </div>
        </div>
    </div>

    <!-- Ranking de Impressoes por Usuario -->
    <div class="section">
        <div class="section-title">Ranking de Impressoes por Usuario</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Qtd Pessoal</th>
                    <th>Pag. Pessoal</th>
                    <th>Custo Pessoal</th>
                    <th>Qtd Admin</th>
                    <th>Pag. Admin</th>
                    <th>Custo Admin</th>
                    <th>Total Pag.</th>
                    <th>% Pessoal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ranking as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ $row->usuario }}</strong></td>
                        <td>{{ $row->qtd_pessoal }}</td>
                        <td>{{ number_format($row->paginas_pessoal, 0, ',', '.') }}</td>
                        <td>R$ {{ number_format($row->custo_pessoal, 2, ',', '.') }}</td>
                        <td>{{ $row->qtd_admin }}</td>
                        <td>{{ number_format($row->paginas_admin, 0, ',', '.') }}</td>
                        <td>R$ {{ number_format($row->custo_admin, 2, ',', '.') }}</td>
                        <td><strong>{{ number_format($row->total_paginas, 0, ',', '.') }}</strong></td>
                        <td>
                            @if($row->total_paginas > 0)
                                {{ number_format(($row->paginas_pessoal / $row->total_paginas) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Listagem Analitica por Usuario -->
    <div class="section" style="page-break-before: always;">
        <div class="section-title">Listagem Analitica por Usuario</div>
        <table>
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Documento</th>
                    <th>Pag.</th>
                    <th>Custo</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                @php $usuarioAtual = null; @endphp
                @foreach($analitico as $log)
                    @if($usuarioAtual !== $log->usuario)
                        @php $usuarioAtual = $log->usuario; @endphp
                        <tr style="background: #e2e8f0;">
                            <td colspan="5"><strong>{{ $log->usuario }}</strong></td>
                        </tr>
                    @endif
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($log->data_impressao)->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->documento }}</td>
                        <td>{{ $log->paginas }}</td>
                        <td>R$ {{ number_format($log->custo, 2, ',', '.') }}</td>
                        <td>
                            @if($log->classificacao === 'PESSOAL')
                                <span class="badge-pessoal">PESSOAL</span>
                            @else
                                <span class="badge-admin">ADMIN</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Rodape -->
    <div class="footer">
        Documento gerado automaticamente pelo Catalogador de Impressoes GAP &mdash; Uso interno
    </div>

</body>
</html>
