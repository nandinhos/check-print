<div class="space-y-6">

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4 xl:grid-cols-5">

        {{-- Total de Impressoes --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Impressoes</p>
            <p class="text-2xl font-bold text-slate-800 mt-1 font-mono">{{ number_format($totalImpressoes, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ number_format($totalPaginas, 0, ',', '.') }} paginas</p>
        </div>

        {{-- Custo Total --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Custo Total</p>
            <p class="text-2xl font-bold text-slate-800 mt-1 font-mono">R$ {{ number_format($custoTotal, 2, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">no periodo selecionado</p>
        </div>

        {{-- Custo Pessoal - destaque --}}
        <div class="bg-amber-50 rounded-xl shadow-sm border border-amber-200 p-5">
            <p class="text-xs font-medium text-amber-700 uppercase tracking-wide">Custo Pessoal</p>
            <p class="text-2xl font-bold text-amber-800 mt-1 font-mono">R$ {{ number_format($custoPessoal, 2, ',', '.') }}</p>
            <p class="text-xs text-amber-600 mt-1">{{ $percentualPessoal }}% do total</p>
        </div>

        {{-- Custo Administrativo --}}
        <div class="bg-violet-50 rounded-xl shadow-sm border border-violet-200 p-5">
            <p class="text-xs font-medium text-violet-700 uppercase tracking-wide">Administrativo</p>
            <p class="text-2xl font-bold text-violet-800 mt-1 font-mono">R$ {{ number_format($custoAdministrativo, 2, ',', '.') }}</p>
            <p class="text-xs text-violet-600 mt-1">{{ 100 - $percentualPessoal }}% do total</p>
        </div>

        {{-- Percentual visual --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 lg:col-span-2 xl:col-span-1">
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">% Pessoal</p>
            <p class="text-2xl font-bold mt-1 font-mono {{ $percentualPessoal > 20 ? 'text-amber-700' : 'text-slate-800' }}">
                {{ $percentualPessoal }}%
            </p>
            <div class="mt-2 h-2 bg-slate-100 rounded-full overflow-hidden">
                <div
                    class="h-full bg-amber-400 rounded-full transition-all duration-500"
                    style="width: {{ min($percentualPessoal, 100) }}%"
                ></div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

            {{-- Date Range --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Data Inicio</label>
                <input
                    type="date"
                    wire:model.live="dataInicio"
                    class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Data Fim</label>
                <input
                    type="date"
                    wire:model.live="dataFim"
                    class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
            </div>

            {{-- Presets --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Periodo Rapido</label>
                <div class="flex gap-1.5">
                    <button wire:click="setPreset('ultimos30')" class="flex-1 text-xs px-2 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">30d</button>
                    <button wire:click="setPreset('esteMes')" class="flex-1 text-xs px-2 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">Mes</button>
                    <button wire:click="setPreset('anoAtual')" class="flex-1 text-xs px-2 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 text-slate-600 transition-colors">Ano</button>
                </div>
            </div>

            {{-- Tipo Toggle --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Tipo</label>
                <select
                    wire:model.live="filtroTipo"
                    class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
                    <option value="todos">Todos</option>
                    <option value="PESSOAL">Apenas Pessoais</option>
                    <option value="ADMINISTRATIVO">Apenas Administrativos</option>
                </select>
            </div>

            {{-- Busca por usuario --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Filtrar por Usuario</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="filtroUsuario"
                    placeholder="Nome do usuario..."
                    class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
            </div>

            {{-- Busca por documento --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Buscar por Documento</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="buscaDocumento"
                    placeholder="Nome do documento..."
                    class="w-full text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                >
            </div>

            {{-- Botoes de exportacao --}}
            <div class="flex items-end gap-2">
                <a
                    href="{{ route('export.excel') }}?data_inicio={{ $dataInicio }}&data_fim={{ $dataFim }}&usuario={{ $filtroUsuario }}&tipo={{ $filtroTipo }}&documento={{ $buscaDocumento }}"
                    class="flex items-center gap-1.5 px-3 py-2 border border-slate-300 text-slate-600 text-xs rounded-lg hover:bg-slate-50 transition-colors"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel
                </a>
                <a
                    href="{{ route('export.pdf') }}?data_inicio={{ $dataInicio }}&data_fim={{ $dataFim }}&usuario={{ $filtroUsuario }}&tipo={{ $filtroTipo }}"
                    class="flex items-center gap-1.5 px-3 py-2 border border-slate-300 text-slate-600 text-xs rounded-lg hover:bg-slate-50 transition-colors"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Tabela Principal --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

        {{-- Header da tabela --}}
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">
                Registros de Impressao
                <span class="ml-2 text-xs font-normal text-slate-400">{{ $logs->total() }} registros</span>
            </h3>
            <div wire:loading class="text-xs text-slate-400 flex items-center gap-1.5">
                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Atualizando...
            </div>
        </div>

        @if($logs->isEmpty())
            <div class="px-6 py-16 text-center">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-medium text-slate-500">Nenhum registro encontrado</p>
                <p class="text-xs text-slate-400 mt-1">Importe um CSV ou ajuste os filtros</p>
                <a href="{{ route('import') }}" class="mt-4 inline-flex items-center gap-1.5 text-xs text-primary-600 hover:text-primary-800">
                    Ir para importacao
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">Data</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">Usuario</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wide">Documento</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wide">Pag.</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wide">Custo</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wide">Classificacao</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($logs as $log)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-4 py-3 text-xs text-slate-500 font-mono whitespace-nowrap">
                                    {{ $log->data_impressao->format('d/m/Y') }}
                                    <span class="text-slate-400">{{ $log->data_impressao->format('H:i') }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-slate-700">{{ $log->usuario }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 max-w-xs">
                                    <span class="truncate block" title="{{ $log->documento }}">{{ $log->documento }}</span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600 text-center font-mono">{{ $log->paginas }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-slate-700 text-right">
                                    R$ {{ number_format($log->custo, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        wire:click="alternarClassificacao({{ $log->id }})"
                                        title="Clique para alternar classificacao{{ $log->isManual() ? ' (editado manualmente)' : '' }}"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium transition-all hover:scale-105
                                            {{ $log->classificacao === 'PESSOAL'
                                                ? 'bg-amber-100 text-amber-800 hover:bg-amber-200'
                                                : 'bg-violet-100 text-violet-800 hover:bg-violet-200' }}"
                                    >
                                        {{ $log->classificacao }}
                                        @if($log->isManual())
                                            <span title="Editado manualmente" class="w-1.5 h-1.5 bg-current rounded-full opacity-60"></span>
                                        @endif
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginacao --}}
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
