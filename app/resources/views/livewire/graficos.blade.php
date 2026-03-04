<div class="space-y-6">

    {{-- Seletor de Ano --}}
    <x-ui.card :glow="false" class="p-6">
        <div class="flex flex-wrap items-center justify-between gap-6">
            <div class="flex flex-col gap-1.5 group">
                <label class="block text-[10px] font-bold text-muted uppercase tracking-widest ml-1">Ano de Referência</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-[18px] transition-colors group-focus-within:text-brand-500">calendar_today</span>
                    <select wire:model.live="ano" class="bg-white/40 dark:bg-zinc-900/40 border border-glass backdrop-blur-md rounded-2xl text-xs font-bold uppercase py-3 pl-12 pr-10 text-main focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500/50 focus:bg-white dark:focus:bg-zinc-900 transition-all appearance-none">
                        @foreach($anosDisponiveis as $anoOpcao)
                            <option value="{{ $anoOpcao }}">{{ $anoOpcao }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex-1 max-w-sm">
                <div class="flex items-start gap-3 p-3 rounded-2xl bg-brand-500/5 border border-brand-500/10">
                    <span class="material-symbols-outlined text-brand-500 text-[18px] shrink-0">info</span>
                    <p class="text-[10px] font-bold text-muted uppercase tracking-tight leading-relaxed">
                        A linha tracejada indica a média mensal — utilize como referência para dimensionamento de novos contratos com a locadora.
                    </p>
                </div>
            </div>

            <div wire:loading class="flex items-center gap-2">
                <span class="material-symbols-outlined animate-spin text-brand-500 text-[18px]">progress_activity</span>
                <span class="text-[10px] font-bold text-muted uppercase tracking-widest">Sincronizando...</span>
            </div>
        </div>
    </x-ui.card>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <x-ui.statistic label="Total Adm. {{ $ano }}" :value="number_format(array_sum($dadosAdministrativo['valores'] ?? []), 0, ',', '.')" trend="Páginas" trend-color="brand" />
        <x-ui.statistic label="Média Adm. / Mês" :value="number_format($dadosAdministrativo['media'] ?? 0, 1, ',', '.')" trend="Mensal" trend-color="brand" />
        <x-ui.statistic label="Total Pessoal {{ $ano }}" :value="number_format(array_sum($dadosPessoal['valores'] ?? []), 0, ',', '.')" trend="Páginas" trend-color="amber" />
        <x-ui.statistic label="Média Pessoal / Mês" :value="number_format($dadosPessoal['media'] ?? 0, 1, ',', '.')" trend="Mensal" trend-color="amber" />
    </div>

    {{-- Graficos --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

        {{-- Grafico Administrativo --}}
        <x-ui.card :glow="true" class="p-8" style="--glow-color: rgba(99,102,241,0.1)">
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-1">
                    <div class="size-2 rounded-full bg-brand-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]"></div>
                    <h3 class="text-lg font-bold font-display text-main tracking-tight">Impressões Administrativas</h3>
                </div>
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Relatório mensal de páginas — Exercício {{ $ano }}</p>
            </div>
            <div wire:ignore class="h-[300px]">
                <canvas id="chart-administrativo"></canvas>
            </div>
        </x-ui.card>

        {{-- Grafico Pessoal --}}
        <x-ui.card :glow="true" class="p-8" style="--glow-color: rgba(245,158,11,0.1)">
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-1">
                    <div class="size-2 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div>
                    <h3 class="text-lg font-bold font-display text-main tracking-tight">Impressões Pessoais</h3>
                </div>
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Relatório mensal de páginas — Exercício {{ $ano }}</p>
            </div>
            <div wire:ignore class="h-[300px]">
                <canvas id="chart-pessoal"></canvas>
            </div>
        </x-ui.card>
    </div>

    @script
    <script>
        let adminChart  = null;
        let pessoalChart = null;

        function buildConfig(dados, label, barColor) {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

            return {
                type: 'bar',
                data: {
                    labels: dados.labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: label,
                            data: dados.valores,
                            backgroundColor: barColor + '44',
                            borderColor: barColor,
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            hoverBackgroundColor: barColor,
                        },
                        {
                            type: 'line',
                            label: 'Media mensal',
                            data: new Array(12).fill(dados.media),
                            borderColor: isDark ? '#ffffff44' : '#00000044',
                            borderDash: [8, 5],
                            borderWidth: 2,
                            pointRadius: 0,
                            tension: 0,
                            fill: false,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                color: textColor,
                                font: { size: 10, weight: 'bold', family: "'Space Grotesk', sans-serif" },
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 6,
                                padding: 20
                            },
                        },
                        tooltip: {
                            backgroundColor: isDark ? 'rgba(15, 23, 42, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                            titleColor: isDark ? '#f8fafc' : '#0f172a',
                            bodyColor: isDark ? '#94a3b8' : '#64748b',
                            borderColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: true,
                            callbacks: {
                                label: (ctx) => ` ${ctx.dataset.label}: ${Number(ctx.parsed.y).toLocaleString('pt-BR')} pag.`,
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: textColor,
                                font: { size: 10, family: "'JetBrains Mono', monospace" }
                            },
                            grid: { color: gridColor, drawBorder: false },
                        },
                        x: {
                            ticks: {
                                color: textColor,
                                font: { size: 10, font: 'bold', family: "'Space Grotesk', sans-serif" }
                            },
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

            if (adminEl)   { adminChart   = new Chart(adminEl,   buildConfig(dataAdmin,   'IMPRESSÕES ADM.',     '#6366f1')); }
            if (pessoalEl) { pessoalChart = new Chart(pessoalEl, buildConfig(dataPessoal, 'IMPRESSÕES PESSOAIS', '#F59E0B')); }
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
