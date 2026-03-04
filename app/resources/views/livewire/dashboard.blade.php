<div class="space-y-8">

    {{-- Modal de edicao de classificacao --}}
    <x-ui.modal name="edit-classification" :show="$modalAberto" max-width="md" focusable>
        <div class="p-8">
            {{-- Header --}}
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold font-display text-main tracking-tight">Alterar Classificação</h2>
                    <p class="text-[10px] font-bold text-muted uppercase tracking-widest mt-1">Escolha a categoria correta para este registro</p>
                </div>
                <button @click="show = false" wire:click="fecharModal" class="text-muted hover:text-main transition-colors p-1 -mr-1 -mt-1">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- Informacoes do registro --}}
            <div class="bg-white/40 dark:bg-white/5 border border-glass rounded-2xl p-5 mb-6 space-y-3 backdrop-blur-md shadow-sm">
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-bold text-muted uppercase tracking-wider">Usuário</span>
                    <span class="text-sm font-bold text-main">{{ $modalUsuario }}</span>
                </div>
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] font-bold text-muted uppercase tracking-wider">Documento</span>
                    <span class="text-xs text-secondary leading-relaxed font-mono line-clamp-2">{{ $modalDocumento }}</span>
                </div>
                <div class="flex flex-col gap-1 pt-1">
                    <span class="text-[10px] font-bold text-muted uppercase tracking-wider">Classificação Atual</span>
                    <div>
                        <x-ui.badge :variant="$modalClassificacaoAtual === 'PESSOAL' ? 'warning' : 'brand'" :pulse="true">
                            {{ $modalClassificacaoAtual }}
                        </x-ui.badge>
                    </div>
                </div>
            </div>

            {{-- Botoes de selecao --}}
            <p class="text-[10px] font-bold text-muted uppercase tracking-widest mb-4 ml-1">Selecione a nova classificação:</p>
            <div class="grid grid-cols-1 gap-3 mb-8">
                <button
                    wire:click="salvarClassificacao('PESSOAL')"
                    class="group flex items-center gap-4 p-4 rounded-2xl border transition-all text-left
                        {{ $modalClassificacaoAtual === 'PESSOAL'
                            ? 'bg-brand-500/10 border-brand-500/40 ring-4 ring-brand-500/5 shadow-lg'
                            : 'bg-white/20 dark:bg-white/5 border-glass border hover:bg-white/40 dark:hover:bg-white/10 hover:border-brand-500/30' }}"
                >
                    <div class="size-12 rounded-xl {{ $modalClassificacaoAtual === 'PESSOAL' ? 'bg-brand-500 text-white' : 'bg-brand-500/10 text-brand-500' }} flex items-center justify-center shrink-0 transition-colors">
                        <span class="material-symbols-outlined text-[24px]">person</span>
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-main tracking-tight">PESSOAL</span>
                        <span class="block text-[10px] text-muted leading-tight mt-0.5">Documentos privados de uso estritamente pessoal</span>
                    </div>
                    @if($modalClassificacaoAtual === 'PESSOAL')
                        <span class="material-symbols-outlined ml-auto text-brand-500">check_circle</span>
                    @endif
                </button>

                <button
                    wire:click="salvarClassificacao('ADMINISTRATIVO')"
                    class="group flex items-center gap-4 p-4 rounded-2xl border transition-all text-left
                        {{ $modalClassificacaoAtual === 'ADMINISTRATIVO'
                            ? 'bg-brand-500/10 border-brand-500/40 ring-4 ring-brand-500/5 shadow-lg'
                            : 'bg-white/20 dark:bg-white/5 border-glass border hover:bg-white/40 dark:hover:bg-white/10 hover:border-brand-500/30' }}"
                >
                    <div class="size-12 rounded-xl {{ $modalClassificacaoAtual === 'ADMINISTRATIVO' ? 'bg-brand-500 text-white' : 'bg-brand-500/10 text-brand-500' }} flex items-center justify-center shrink-0 transition-colors">
                        <span class="material-symbols-outlined text-[24px]">business_center</span>
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-main tracking-tight">ADMINISTRATIVO</span>
                        <span class="block text-[10px] text-muted leading-tight mt-0.5">Documentos corporativos e operacionais</span>
                    </div>
                    @if($modalClassificacaoAtual === 'ADMINISTRATIVO')
                        <span class="material-symbols-outlined ml-auto text-brand-500">check_circle</span>
                    @endif
                </button>
            </div>

            {{-- Footer Acoes --}}
            <div class="flex gap-3">
                <x-ui.button variant="ghost" class="flex-1" @click="show = false" wire:click="fecharModal">
                    Cancelar
                </x-ui.button>
            </div>
        </div>
    </x-ui.modal>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
        <x-ui.statistic label="Total Impressões" :value="number_format($totalImpressoes, 0, ',', '.')" :trend="number_format($totalPaginas, 0, ',', '.') . ' Pág.'" trend-color="slate" />
        <x-ui.statistic label="Custo Total" :value="'R$ ' . number_format($custoTotal, 2, ',', '.')" trend="Período" trend-color="brand" />
        <x-ui.statistic label="Custo Pessoal" :value="'R$ ' . number_format($custoPessoal, 2, ',', '.')" :trend="$percentualPessoal . '%'" trend-color="amber" />
        <x-ui.statistic label="Custo Administrativo" :value="'R$ ' . number_format($custoAdministrativo, 2, ',', '.')" :trend="(100 - $percentualPessoal) . '%'" trend-color="brand" />

        {{-- Percentual visual Card --}}
        <x-ui.card class="p-4" :glow="true" style="--glow-color: rgba(245,158,11,0.15)">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] font-bold text-muted uppercase tracking-widest">% Pessoais</span>
                <span class="text-lg font-black font-display {{ $percentualPessoal > 20 ? 'text-amber-500' : 'text-main' }}">
                    {{ $percentualPessoal }}%
                </span>
            </div>
            <div class="h-1.5 bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                <div
                    class="h-full bg-amber-500 rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(245,158,11,0.5)]"
                    style="width: {{ min($percentualPessoal, 100) }}%"
                ></div>
            </div>
            <p class="text-[9px] text-muted mt-2 font-bold uppercase tracking-tighter">Budget Alvo: < 15%</p>
        </x-ui.card>
    </div>

    {{-- Filtros --}}
    <x-ui.card :glow="false" class="p-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 items-end">
            <x-ui.input label="Data Início" type="date" wire:model.live="dataInicio" />
            <x-ui.input label="Data Fim" type="date" wire:model.live="dataFim" />

            <div class="flex flex-col gap-1.5 group">
                <label class="block text-xs font-bold text-muted uppercase tracking-wider ml-1">Tipo</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-[20px] transition-colors group-focus-within:text-brand-500">filter_alt</span>
                    <select wire:model.live="filtroTipo" class="w-full bg-white/40 dark:bg-zinc-900/40 border border-glass backdrop-blur-md rounded-2xl text-xs font-bold uppercase py-3 pl-12 pr-4 text-main focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500/50 focus:bg-white dark:focus:bg-zinc-900 transition-all appearance-none">
                        <option value="todos">Todos os Tipos</option>
                        <option value="PESSOAL">Pessoais</option>
                        <option value="ADMINISTRATIVO">Administrativos</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col gap-1.5 group">
                <label class="block text-xs font-bold text-muted uppercase tracking-wider ml-1">Usuário</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-muted text-[20px] transition-colors group-focus-within:text-brand-500">person</span>
                    <select wire:model.live="filtroUsuario" class="w-full bg-white/40 dark:bg-zinc-900/40 border border-glass backdrop-blur-md rounded-2xl text-xs font-bold uppercase py-3 pl-12 pr-4 text-main focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500/50 focus:bg-white dark:focus:bg-zinc-900 transition-all appearance-none">
                        <option value="">Todos os Usuários</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario }}">{{ $usuario }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <x-ui.button variant="success" class="flex-1" icon="table_view" href="{{ route('export.excel') }}?data_inicio={{ $dataInicio }}&data_fim={{ $dataFim }}&usuario={{ $filtroUsuario }}&tipo={{ $filtroTipo }}&documento={{ $buscaDocumento }}">
                    Excel
                </x-ui.button>
                <x-ui.button variant="danger" class="flex-1" icon="picture_as_pdf" href="{{ route('export.pdf') }}?data_inicio={{ $dataInicio }}&data_fim={{ $dataFim }}&usuario={{ $filtroUsuario }}&tipo={{ $filtroTipo }}">
                    PDF
                </x-ui.button>
            </div>
        </div>
    </x-ui.card>

    {{-- Tabela Principal --}}
    <x-ui.card :glow="false" class="overflow-hidden">
        {{-- Header da tabela --}}
        <div class="px-8 py-5 border-b border-glass flex items-center justify-between bg-white/40 dark:bg-white/5 backdrop-blur-md">
            <div class="flex items-center gap-4">
                <div class="flex flex-col">
                    <h3 class="text-sm font-bold text-main font-display tracking-tight">Registros de Impressão</h3>
                    <p class="text-[10px] font-bold text-muted uppercase tracking-widest mt-0.5">{{ $logs->total() }} registros encontrados</p>
                </div>

                @if(count($selecionados) > 0)
                    <x-ui.badge variant="brand" :pulse="true">
                        {{ count($selecionados) }} selecionados
                    </x-ui.badge>
                @endif
            </div>

            <div class="flex items-center gap-3">
                @if(count($selecionados) > 0)
                    <div class="flex items-center gap-2 pr-4 border-r border-glass">
                        <span class="text-[9px] font-black uppercase tracking-widest text-muted">Ações:</span>
                        <x-ui.button variant="primary" size="xs" icon="person" wire:click="salvarClassificacaoEmMassa({{ json_encode($selecionados) }}, 'PESSOAL')">
                            Pessoal
                        </x-ui.button>
                        <x-ui.button variant="primary" size="xs" icon="business_center" wire:click="salvarClassificacaoEmMassa({{ json_encode($selecionados) }}, 'ADMINISTRATIVO')">
                            Adm
                        </x-ui.button>
                        <button wire:click="$set('selecionados', [])" class="p-1.5 text-muted hover:text-rose-500 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                @endif

                <div wire:loading class="flex items-center gap-2">
                    <span class="material-symbols-outlined animate-spin text-brand-500 text-[18px]">progress_activity</span>
                    <span class="text-[10px] font-bold text-muted uppercase tracking-widest">Sincronizando...</span>
                </div>
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
                    @php $idsNaPagina = $logs->pluck('id')->toArray(); @endphp
                    <thead>
                        <tr class="bg-white/40 dark:bg-white/5 border-b border-glass">
                            <th class="px-6 py-4 w-10">
                                <input
                                    type="checkbox"
                                    x-data="{ checkAll: false }"
                                    @click="checkAll = !checkAll; $wire.toggleTodos({{ json_encode($idsNaPagina) }})"
                                    :checked="checkAll"
                                    class="peer relative h-5 w-5 cursor-pointer appearance-none rounded-md border border-glass bg-white/40 dark:bg-black/20 transition-all checked:border-brand-500 checked:bg-brand-500 focus:outline-none"
                                >
                            </th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-muted uppercase tracking-widest">Data / Hora</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-muted uppercase tracking-widest">Usuário</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-muted uppercase tracking-widest">Documento</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-muted uppercase tracking-widest">Pág.</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-muted uppercase tracking-widest">Custo</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-muted uppercase tracking-widest">Classificação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-glass">
                        @foreach($logs as $log)
                            @php $selecionado = in_array($log->id, $selecionados); @endphp
                            <tr class="{{ $selecionado ? 'bg-brand-500/5' : 'hover:bg-white/30 dark:hover:bg-white/5' }} transition-colors group">
                                <td class="px-6 py-4">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selecionados"
                                        value="{{ $log->id }}"
                                        class="peer relative h-5 w-5 cursor-pointer appearance-none rounded-md border border-glass bg-white/40 dark:bg-black/20 transition-all checked:border-brand-500 checked:bg-brand-500 focus:outline-none"
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-main mono-text">{{ $log->data_impressao->format('d/m/Y') }}</span>
                                        <span class="text-[10px] text-muted font-bold uppercase tracking-tight">{{ $log->data_impressao->format('H:i') }} h</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="size-7 rounded-full bg-brand-500/10 flex items-center justify-center border border-brand-500/20 text-brand-600 font-bold text-[10px]">
                                            {{ substr($log->usuario, 0, 1) }}
                                        </div>
                                        <span class="text-xs font-bold text-main">{{ $log->usuario }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 max-w-xs">
                                    <span class="text-xs text-secondary line-clamp-1 hover:line-clamp-none transition-all leading-tight" title="{{ $log->documento }}">
                                        {{ $log->documento }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[11px] font-black mono-text bg-white/40 dark:bg-white/10 px-2 py-0.5 rounded-lg border border-glass">
                                        {{ $log->paginas }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xs font-black mono-text text-main">
                                        R$ {{ number_format($log->custo, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <button
                                            wire:click="abrirModalEdicao({{ $log->id }})"
                                            class="transition-transform hover:scale-105 active:scale-95"
                                        >
                                            <x-ui.badge
                                                :variant="$log->classificacao === 'PESSOAL' ? 'warning' : 'primary'"
                                                :pulse="$log->isManual()"
                                            >
                                                {{ $log->classificacao }}
                                            </x-ui.badge>
                                        </button>

                                        @if($log->isManual())
                                            <button
                                                wire:click="reverterClassificacao({{ $log->id }})"
                                                title="Reverter para: {{ $log->classificacao_auto }}"
                                                class="opacity-0 group-hover:opacity-100 transition-opacity text-muted hover:text-brand-500 p-1"
                                            >
                                                <span class="material-symbols-outlined text-[16px]">history</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endif
    </x-ui.card>
</div>
