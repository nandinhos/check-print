<div class="space-y-6">

    {{-- Seletor de Ano --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-slate-600">Ano de referencia:</label>
                <select
                    wire:model.live="ano"
                    class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
                    @foreach($anosDisponiveis as $anoOpcao)
                        <option value="{{ $anoOpcao }}">{{ $anoOpcao }}</option>
                    @endforeach
                </select>
            </div>
            <p class="text-xs text-slate-400">
                A linha tracejada indica a media mensal — use como referencia para dimensionamento de contrato.
            </p>
            <div wire:loading class="text-xs text-slate-400 flex items-center gap-1.5 ml-auto">
                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Atualizando...
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="bg-violet-50 rounded-xl border border-violet-200 p-5">
            <p class="text-xs font-medium text-violet-700 uppercase tracking-wide">Total Adm. {{ $ano }}</p>
            <p class="text-2xl font-bold text-violet-800 mt-1 font-mono">
                {{ number_format(array_sum($dadosAdministrativo['valores'] ?? []), 0, ',', '.') }}
            </p>
            <p class="text-xs text-violet-500 mt-1">paginas impressas</p>
        </div>
        <div class="bg-violet-50 rounded-xl border border-violet-200 p-5">
            <p class="text-xs font-medium text-violet-700 uppercase tracking-wide">Media Adm. / Mes</p>
            <p class="text-2xl font-bold text-violet-800 mt-1 font-mono">
                {{ number_format($dadosAdministrativo['media'] ?? 0, 1, ',', '.') }}
            </p>
            <p class="text-xs text-violet-500 mt-1">paginas / mes</p>
        </div>
        <div class="bg-amber-50 rounded-xl border border-amber-200 p-5">
            <p class="text-xs font-medium text-amber-700 uppercase tracking-wide">Total Pessoal {{ $ano }}</p>
            <p class="text-2xl font-bold text-amber-800 mt-1 font-mono">
                {{ number_format(array_sum($dadosPessoal['valores'] ?? []), 0, ',', '.') }}
            </p>
            <p class="text-xs text-amber-500 mt-1">paginas impressas</p>
        </div>
        <div class="bg-amber-50 rounded-xl border border-amber-200 p-5">
            <p class="text-xs font-medium text-amber-700 uppercase tracking-wide">Media Pessoal / Mes</p>
            <p class="text-2xl font-bold text-amber-800 mt-1 font-mono">
                {{ number_format($dadosPessoal['media'] ?? 0, 1, ',', '.') }}
            </p>
            <p class="text-xs text-amber-500 mt-1">paginas / mes</p>
        </div>
    </div>

    {{-- Graficos --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Grafico Administrativo --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="mb-5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-3 h-3 rounded-full bg-violet-500"></span>
                    <h3 class="text-sm font-semibold text-slate-800">Impressoes Administrativas</h3>
                </div>
                <p class="text-xs text-slate-400">Paginas por mes em {{ $ano }}</p>
            </div>
            <div wire:ignore>
                <canvas id="chart-administrativo"></canvas>
            </div>
        </div>

        {{-- Grafico Pessoal --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="mb-5">
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <h3 class="text-sm font-semibold text-slate-800">Impressoes Pessoais</h3>
                </div>
                <p class="text-xs text-slate-400">Paginas por mes em {{ $ano }}</p>
            </div>
            <div wire:ignore>
                <canvas id="chart-pessoal"></canvas>
            </div>
        </div>
    </div>

    @script
    <script>
        let adminChart  = null;
        let pessoalChart = null;

        function buildConfig(dados, label, barColor) {
            return {
                type: 'bar',
                data: {
                    labels: dados.labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: label,
                            data: dados.valores,
                            backgroundColor: barColor + '33',
                            borderColor: barColor,
                            borderWidth: 1.5,
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                        {
                            type: 'line',
                            label: 'Media mensal',
                            data: new Array(12).fill(dados.media),
                            borderColor: '#94a3b8',
                            borderDash: [6, 4],
                            borderWidth: 2,
                            pointRadius: 0,
                            tension: 0,
                            fill: false,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { font: { size: 11 }, usePointStyle: true, pointStyleWidth: 10 },
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ` ${ctx.dataset.label}: ${Number(ctx.parsed.y).toLocaleString('pt-BR')} pag.`,
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 11 } },
                            grid: { color: '#f1f5f9' },
                        },
                        x: {
                            ticks: { font: { size: 11 } },
                            grid: { display: false },
                        },
                    },
                },
            };
        }

        function criarGraficos(dataAdmin, dataPessoal) {
            if (adminChart)   { adminChart.destroy();   adminChart   = null; }
            if (pessoalChart) { pessoalChart.destroy(); pessoalChart = null; }

            const adminEl   = document.getElementById('chart-administrativo');
            const pessoalEl = document.getElementById('chart-pessoal');

            if (adminEl)   { adminChart   = new Chart(adminEl,   buildConfig(dataAdmin,   'Impressoes Adm.',     '#8B5CF6')); }
            if (pessoalEl) { pessoalChart = new Chart(pessoalEl, buildConfig(dataPessoal, 'Impressoes Pessoais', '#F59E0B')); }
        }

        criarGraficos(
            @json($dadosAdministrativo),
            @json($dadosPessoal)
        );

        $wire.on('graficos-atualizados', ({ admin, pessoal }) => {
            criarGraficos(admin, pessoal);
        });
    </script>
    @endscript

</div>
