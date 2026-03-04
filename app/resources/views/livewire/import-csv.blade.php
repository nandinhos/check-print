<div class="max-w-4xl space-y-8">

    {{-- Banner: Download do modelo --}}
    <x-ui.card :glow="true" class="p-6 overflow-hidden" style="--glow-color: rgba(99,102,241,0.15)">
        <div class="flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="size-12 rounded-2xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
                    <span class="material-symbols-outlined text-[28px]">description</span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-main font-display tracking-tight">Arquivo Modelo CSV</h3>
                    <p class="text-[10px] font-bold text-muted uppercase tracking-widest mt-0.5">
                        Formato exigido: <code class="font-mono bg-white/40 dark:bg-white/5 border border-glass px-1.5 py-0.5 rounded text-secondary">Data;Hora;Usuário;Documento;Páginas;Custo;Aplicativo</code>
                    </p>
                </div>
            </div>
            <x-ui.button variant="primary" icon="download" href="{{ route('export.modelo-csv') }}" download>
                Baixar Modelo
            </x-ui.button>
        </div>
    </x-ui.card>

    {{-- Card principal --}}
    <x-ui.card :glow="false" class="p-8">
        <div class="mb-8">
            <h3 class="text-lg font-bold text-main font-display tracking-tight">Importar Base de Dados</h3>
            <p class="text-[10px] font-bold text-muted uppercase tracking-widest mt-1">
                Separador: <span class="mono-text bg-white/40 dark:bg-white/5 border border-glass px-1 rounded mx-0.5">;</span> ou <span class="mono-text bg-white/40 dark:bg-white/5 border border-glass px-1 rounded mx-0.5">,</span>
                &bull; Datas: <span class="mono-text bg-white/40 dark:bg-white/5 border border-glass px-1 rounded mx-0.5 ml-1">DD/MM/AAAA</span>
            </p>
        </div>

        {{-- Estados: idle / validating / error --}}
        @if(in_array($status, ['idle', 'validating', 'error']))

            {{-- Dropzone: aparece enquanto nao ha preview --}}
            @if(! $mostrarPreview)
                <div
                    x-data="{ dragging: false }"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="dragging = false"
                    :class="dragging ? 'border-brand-500 bg-brand-500/5 ring-4 ring-brand-500/10' : 'border-glass hover:border-brand-500/40 hover:bg-white/30 dark:hover:bg-white/5'"
                    class="border-2 border-dashed rounded-3xl p-16 text-center transition-all cursor-pointer group relative overflow-hidden"
                    onclick="document.getElementById('csv-input').click()"
                >
                    <input id="csv-input" type="file" accept=".csv,.txt" class="hidden" wire:model="file">
                    
                    <div class="relative z-10 flex flex-col items-center gap-6">
                        <div class="size-20 rounded-full bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-500 group-hover:scale-110 group-hover:bg-brand-500 group-hover:text-white transition-all duration-500">
                            <span class="material-symbols-outlined text-[40px]">upload_file</span>
                        </div>
                        <div>
                            <p class="text-base font-bold text-main tracking-tight group-hover:text-brand-500 transition-colors">Arraste ou Clique para enviar CSV</p>
                            <p class="text-[10px] font-bold text-muted uppercase tracking-widest mt-1">Limites: UTF-8 &bull; Máximo 50 MB</p>
                        </div>
                    </div>

                    <div wire:loading wire:target="file" class="absolute inset-0 z-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md flex flex-col items-center justify-center gap-4 transition-all">
                        <span class="material-symbols-outlined animate-spin text-brand-500 text-[32px]">progress_activity</span>
                        <span class="text-xs font-bold text-main uppercase tracking-widest">Analisando estrutura do arquivo...</span>
                    </div>
                </div>
            @endif

            {{-- Erro de cabecalho --}}
            @if($status === 'error')
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-red-700">Erro no cabecalho do arquivo</p>
                        <p class="text-sm text-red-600 mt-0.5">{{ $mensagemErro }}</p>
                    </div>
                </div>
                <button wire:click="cancelar" class="mt-3 text-sm text-slate-500 hover:text-slate-700 underline">
                    Tentar outro arquivo
                </button>
            @endif

            @error('file')
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ $message }}</p>
                </div>
            @enderror

            {{-- Preview com validacao linha a linha --}}
            @if($mostrarPreview && count($preview) > 0)
                {{-- Resumo de contagens --}}
                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <x-ui.card class="p-4" :glow="false">
                        <span class="text-[9px] font-black uppercase tracking-widest text-muted block mb-1">Total Linhas</span>
                        <p class="text-2xl font-black font-display text-main mono-text">{{ number_format($total, 0, ',', '.') }}</p>
                    </x-ui.card>
                    <x-ui.card class="p-4 border-brand-500/30 bg-brand-500/5" :glow="false">
                        <span class="text-[9px] font-black uppercase tracking-widest text-brand-600 block mb-1">Válidos</span>
                        <p class="text-2xl font-black font-display text-brand-500 mono-text">{{ number_format($validos, 0, ',', '.') }}</p>
                    </x-ui.card>
                    <x-ui.card class="p-4 {{ $duplicatas > 0 ? 'border-amber-500/30 bg-amber-500/5' : '' }}" :glow="false">
                        <span class="text-[9px] font-black uppercase tracking-widest {{ $duplicatas > 0 ? 'text-amber-600' : 'text-muted' }} block mb-1">Duplicatas</span>
                        <p class="text-2xl font-black font-display {{ $duplicatas > 0 ? 'text-amber-500' : 'text-main/40' }} mono-text">{{ number_format($duplicatas, 0, ',', '.') }}</p>
                    </x-ui.card>
                    <x-ui.card class="p-4 {{ $invalidos > 0 ? 'border-rose-500/30 bg-rose-500/5' : '' }}" :glow="false">
                        <span class="text-[9px] font-black uppercase tracking-widest {{ $invalidos > 0 ? 'text-rose-600' : 'text-muted' }} block mb-1">Inválidos</span>
                        <p class="text-2xl font-black font-display {{ $invalidos > 0 ? 'text-rose-500' : 'text-main/40' }} mono-text">{{ number_format($invalidos, 0, ',', '.') }}</p>
                    </x-ui.card>
                </div>

                {{-- Painel de erros por linha (colapsavel) --}}
                @if($invalidos > 0)
                    <div class="mt-6">
                        <button
                            @click="$wire.set('mostrarErros', !@js($mostrarErros))"
                            class="w-full flex items-center justify-between px-6 py-4 bg-rose-500/5 border border-rose-500/20 rounded-2xl transition-all hover:bg-rose-500/10 group"
                        >
                            <span class="text-[10px] font-black uppercase tracking-widest text-rose-600 flex items-center gap-3">
                                <span class="material-symbols-outlined text-[18px]">warning</span>
                                {{ $invalidos }} {{ $invalidos === 1 ? 'linha com problema' : 'linhas com problema' }} &bull; Serão ignoradas
                            </span>
                            <span class="material-symbols-outlined text-rose-500 transition-transform {{ $mostrarErros ? 'rotate-180' : '' }}">expand_more</span>
                        </button>

                        @if($mostrarErros)
                            <div class="mt-2 border border-glass rounded-2xl overflow-hidden divide-y divide-glass max-h-48 overflow-y-auto bg-white/40 dark:bg-white/5 backdrop-blur-md">
                                @foreach($errosPorLinha as $erro)
                                    <div class="px-6 py-3 flex items-start gap-4 hover:bg-white/30 dark:hover:bg-white/5 transition-colors">
                                        <span class="font-mono text-[10px] bg-rose-500/10 text-rose-600 px-2 py-0.5 rounded-lg border border-rose-500/20 flex-shrink-0 mt-0.5 font-bold">
                                            L{{ $erro['linha'] }}
                                        </span>
                                        <ul class="text-[11px] font-bold text-secondary space-y-1">
                                            @foreach($erro['erros'] as $msg)
                                                <li class="flex items-center gap-1.5"><span class="size-1 rounded-full bg-rose-500"></span> {{ $msg }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Painel de duplicatas (colapsavel) --}}
                @if(count($duplicatasPorLinha) > 0)
                    @php $totalDups = count($duplicatasPorLinha); $totalAprovadas = count($duplicatasAprovadas); @endphp
                    <div class="mt-4">
                        <button
                            @click="$wire.set('mostrarDuplicatas', !@js($mostrarDuplicatas))"
                            class="w-full flex items-center justify-between px-6 py-4 border rounded-2xl transition-all hover:bg-opacity-20 group
                                {{ $totalAprovadas === $totalDups ? 'bg-brand-500/5 border-brand-500/20' : 'bg-amber-500/5 border-amber-500/20' }}"
                        >
                            <span class="text-[10px] font-black uppercase tracking-widest flex items-center gap-3
                                {{ $totalAprovadas === $totalDups ? 'text-brand-600' : 'text-amber-600' }}">
                                <span class="material-symbols-outlined text-[18px]">find_replace</span>
                                @if($totalAprovadas === $totalDups)
                                    {{ $totalDups }} Duplicatas Permitidas &bull; Serão importadas
                                @else
                                    {{ $duplicatas }} Registros Conflitantes &bull; {{ $totalAprovadas }} Permitidos
                                @endif
                            </span>
                            <span class="material-symbols-outlined transition-transform {{ $mostrarDuplicatas ? 'rotate-180' : '' }}
                                {{ $totalAprovadas === $totalDups ? 'text-brand-500' : 'text-amber-500' }}">expand_more</span>
                        </button>

                        @if($mostrarDuplicatas)
                            <div class="mt-2 border border-glass rounded-2xl overflow-hidden bg-white/40 dark:bg-white/5 backdrop-blur-md">
                                <div class="px-6 py-4 border-b border-glass flex items-center justify-between gap-4 bg-white/20 dark:bg-white/5">
                                    <p class="text-[10px] font-bold text-muted uppercase tracking-tight">
                                        Selecione os registros que deseja importar mesmo já existindo dados idênticos no banco.
                                    </p>
                                    <button
                                        wire:click="{{ count($duplicatasAprovadas) === count($duplicatasPorLinha) ? 'desaprovarTodasDuplicatas' : 'aprovarTodasDuplicatas' }}"
                                        class="text-[10px] font-black uppercase tracking-widest text-brand-600 hover:text-brand-700 transition-colors whitespace-nowrap"
                                    >
                                        {{ count($duplicatasAprovadas) === count($duplicatasPorLinha) ? 'Desmarcar Tudo' : 'Marcar Tudo' }}
                                    </button>
                                </div>
                                <div class="max-h-64 overflow-y-auto divide-y divide-glass">
                                    @foreach($duplicatasPorLinha as $dup)
                                        @php $aprovado = in_array($dup['fingerprint'], $duplicatasAprovadas, true); @endphp
                                        <div class="px-6 py-4 flex items-center gap-4 transition-colors {{ $aprovado ? 'bg-brand-500/5' : '' }} hover:bg-white/30 dark:hover:bg-white/10">
                                            <input
                                                type="checkbox"
                                                wire:click="toggleDuplicataAprovada('{{ $dup['fingerprint'] }}')"
                                                @checked($aprovado)
                                                class="peer relative h-5 w-5 cursor-pointer appearance-none rounded-md border border-glass bg-white/40 dark:bg-black/20 transition-all checked:border-brand-500 checked:bg-brand-500 focus:outline-none"
                                            >
                                            <div class="flex flex-col flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-mono text-[9px] font-black bg-white/60 dark:bg-white/10 text-main px-1.5 py-0.5 rounded border border-glass uppercase tracking-tighter">L{{ $dup['linha'] }}</span>
                                                    <span class="text-xs font-bold text-main truncate">{{ $dup['usuario'] }}</span>
                                                </div>
                                                <span class="text-[10px] text-muted font-bold truncate mt-0.5">{{ $dup['documento'] }}</span>
                                            </div>
                                            <div class="flex flex-col items-end shrink-0 gap-1">
                                                <span class="text-[10px] font-black text-main mono-text">{{ \Carbon\Carbon::parse($dup['data'])->format('d/m/Y H:i') }}</span>
                                                <x-ui.badge :variant="$aprovado ? 'brand' : 'warning'" size="xs">
                                                    {{ $aprovado ? 'Permitido' : ($dup['origem'] === 'banco' ? 'No Banco' : 'Repetido') }}
                                                </x-ui.badge>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Tabela de preview --}}
                <div class="mt-8">
                    <p class="text-[10px] font-black uppercase tracking-widest text-muted mb-3 ml-1">
                        Preview dos Dados &bull; <span class="text-main">{{ count($preview) }} de {{ $total }} registros</span>
                    </p>
                    <div class="overflow-x-auto rounded-2xl border border-glass bg-white/20 dark:bg-black/20">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-white/40 dark:bg-white/5 border-b border-glass">
                                    <th class="px-4 py-3 text-center w-12 text-[9px] font-black text-muted uppercase tracking-widest">#</th>
                                    <th class="px-4 py-3 text-center w-20 text-[9px] font-black text-muted uppercase tracking-widest">Status</th>
                                    <th class="px-4 py-3 text-left text-[9px] font-black text-muted uppercase tracking-widest">Usuário</th>
                                    <th class="px-4 py-3 text-left text-[9px] font-black text-muted uppercase tracking-widest">Documento</th>
                                    <th class="px-4 py-3 text-left text-[9px] font-black text-muted uppercase tracking-widest">Data / Hora</th>
                                    <th class="px-4 py-3 text-right text-[9px] font-black text-muted uppercase tracking-widest">Pág.</th>
                                    <th class="px-4 py-3 text-right text-[9px] font-black text-muted uppercase tracking-widest">Custo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-glass">
                                @foreach($preview as $row)
                                    @php
                                        $isDup = $row['_duplicata'] ?? false;
                                        $trClass = ! $row['_valido'] ? 'bg-rose-500/5 hover:bg-rose-500/10'
                                                 : ($isDup ? 'bg-amber-500/5 hover:bg-amber-500/10' : 'hover:bg-white/30 dark:hover:bg-white/5');
                                    @endphp
                                    <tr class="{{ $trClass }} transition-colors">
                                        <td class="px-4 py-3 text-center font-mono text-[10px] text-muted">{{ $row['_linha'] }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if(! $row['_valido'])
                                                <x-ui.badge variant="destructive" size="xs" title="{{ implode(' | ', $row['_erros']) }}">Erro</x-ui.badge>
                                            @elseif($isDup)
                                                <x-ui.badge variant="warning" size="xs">Dup.</x-ui.badge>
                                            @else
                                                <x-ui.badge variant="success" size="xs">OK</x-ui.badge>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs font-bold {{ ! $row['_valido'] ? 'text-rose-500' : 'text-main' }}">
                                            {{ $row['usuario'] ?: '—' }}
                                        </td>
                                        <td class="px-4 py-3 max-w-[200px]">
                                            <span class="text-[11px] font-bold text-secondary truncate block" title="{{ $row['documento'] }}">
                                                {{ $row['documento'] ?: '—' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-mono text-[10px] text-main">
                                            {{ $row['data_impressao'] }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-black mono-text text-main text-[11px]">
                                            {{ $row['paginas'] }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-black mono-text text-main text-[11px]">
                                            R$ {{ number_format($row['custo'], 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Acoes --}}
                <div class="mt-10 flex items-center justify-between border-t border-glass pt-8">
                    <x-ui.button variant="ghost" wire:click="cancelar">
                        Cancelar
                    </x-ui.button>
                    <x-ui.button variant="primary" icon="publish" wire:click="importar" :disabled="$validos === 0" class="px-12">
                        Importar {{ $validos }} registros válidos
                    </x-ui.button>
                </div>
            @endif
        @endif

        {{-- Estado: Importando --}}
        @if($status === 'importing')
            <div class="flex flex-col items-center py-20 gap-6">
                <span class="material-symbols-outlined animate-spin text-brand-500 text-[48px]">progress_activity</span>
                <div class="text-center">
                    <p class="text-lg font-bold text-main tracking-tight font-display">Processando Importação</p>
                    <p class="text-[10px] font-bold text-muted uppercase tracking-widest mt-1">Classificando e indexando registros no banco de dados</p>
                </div>
            </div>
        @endif

        {{-- Estado: Sucesso --}}
        @if($status === 'success')
            <div class="flex flex-col items-center py-16 gap-8">
                <div class="size-24 rounded-full bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-500 relative">
                    <div class="absolute inset-0 rounded-full animate-ping bg-brand-500/10"></div>
                    <span class="material-symbols-outlined text-[48px] relative z-10">check_circle</span>
                </div>
                <div class="text-center max-w-sm">
                    <p class="text-2xl font-black text-main tracking-tight font-display">Importação Concluída!</p>
                    <p class="text-xs font-bold text-secondary mt-3 leading-relaxed">
                        Foram importados <span class="text-brand-500 mono-text text-base font-black">{{ $importados }}</span> novos registros com classificação inteligente.
                    </p>
                    <div class="flex flex-col gap-2 mt-6">
                        @if($duplicatas > 0)
                            <div class="flex items-center gap-2 justify-center text-[10px] font-bold text-amber-600 uppercase tracking-tight bg-amber-500/5 px-3 py-1.5 rounded-xl border border-amber-500/10">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                {{ $duplicatas }} Duplicatas ignoradas com segurança
                            </div>
                        @endif
                        @if($ignorados > 0)
                            <div class="flex items-center gap-2 justify-center text-[10px] font-bold text-rose-600 uppercase tracking-tight bg-rose-500/5 px-3 py-1.5 rounded-xl border border-rose-500/10">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                {{ $ignorados }} Linhas inválidas foram descartadas
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-4 mt-4">
                    <x-ui.button variant="secondary" wire:click="cancelar">
                        Nova Importação
                    </x-ui.button>
                    <x-ui.button variant="primary" icon="dashboard" href="{{ route('dashboard') }}">
                        Ver Dashboard
                    </x-ui.button>
                </div>
            </div>
        @endif
    </x-ui.card>
</div>
