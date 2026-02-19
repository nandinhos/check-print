<div class="max-w-3xl space-y-4">

    {{-- Banner: Download do modelo --}}
    <div class="bg-primary-50 border border-primary-200 rounded-xl px-5 py-4 flex items-center justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 bg-primary-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-primary-900">Arquivo Modelo CSV</p>
                <p class="text-xs text-primary-700 mt-0.5">
                    Formato: <code class="font-mono bg-primary-100 px-1 rounded">Data;Hora;Usuario;Documento;Paginas;Custo;Aplicativo</code>
                </p>
            </div>
        </div>
        <a href="{{ route('export.modelo-csv') }}"
           class="flex items-center gap-1.5 px-4 py-2 bg-primary-900 text-white text-xs font-medium rounded-lg hover:bg-primary-800 transition-colors whitespace-nowrap flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Baixar Modelo
        </a>
    </div>

    {{-- Card principal --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="mb-5">
            <h3 class="text-base font-semibold text-slate-800">Importar Arquivo CSV</h3>
            <p class="text-sm text-slate-500 mt-1">
                Separador: <code class="font-mono text-xs bg-slate-100 px-1 rounded">;</code> ou <code class="font-mono text-xs bg-slate-100 px-1 rounded">,</code>
                &mdash; Datas no formato <code class="font-mono text-xs bg-slate-100 px-1 rounded">DD/MM/AAAA</code>
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
                    :class="dragging ? 'border-primary-500 bg-primary-50' : 'border-slate-300 hover:border-primary-400'"
                    class="border-2 border-dashed rounded-xl p-10 text-center transition-colors cursor-pointer"
                    onclick="document.getElementById('csv-input').click()"
                >
                    <input id="csv-input" type="file" accept=".csv,.txt" class="hidden" wire:model="file">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 bg-primary-50 rounded-full flex items-center justify-center">
                            <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-700">Arraste ou Clique para enviar CSV</p>
                            <p class="text-xs text-slate-400 mt-1">Tamanho maximo: 50 MB</p>
                        </div>
                    </div>
                    <div wire:loading wire:target="file" class="mt-4 flex items-center justify-center gap-2 text-primary-600">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="text-sm">Analisando arquivo...</span>
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
                <div class="mt-5 grid grid-cols-3 gap-3">
                    <div class="bg-slate-50 rounded-lg px-4 py-3 text-center border border-slate-200">
                        <p class="text-2xl font-bold font-mono text-slate-800">{{ number_format($total, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">Total de linhas</p>
                    </div>
                    <div class="bg-green-50 rounded-lg px-4 py-3 text-center border border-green-200">
                        <p class="text-2xl font-bold font-mono text-green-700">{{ number_format($validos, 0, ',', '.') }}</p>
                        <p class="text-xs text-green-600 mt-0.5">Linhas validas</p>
                    </div>
                    <div class="rounded-lg px-4 py-3 text-center border {{ $invalidos > 0 ? 'bg-red-50 border-red-200' : 'bg-slate-50 border-slate-200' }}">
                        <p class="text-2xl font-bold font-mono {{ $invalidos > 0 ? 'text-red-700' : 'text-slate-400' }}">{{ number_format($invalidos, 0, ',', '.') }}</p>
                        <p class="text-xs {{ $invalidos > 0 ? 'text-red-600' : 'text-slate-400' }} mt-0.5">Com erro</p>
                    </div>
                </div>

                {{-- Painel de erros por linha (colapsavel) --}}
                @if($invalidos > 0)
                    <div class="mt-4">
                        <button
                            wire:click="$toggle('mostrarErros')"
                            class="w-full flex items-center justify-between px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm hover:bg-red-100 transition-colors"
                        >
                            <span class="font-medium text-red-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                {{ $invalidos }} {{ $invalidos === 1 ? 'linha com problema' : 'linhas com problema' }}
                                — serao ignoradas na importacao
                            </span>
                            <svg class="w-4 h-4 text-red-500 transition-transform {{ $mostrarErros ? 'rotate-180' : '' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        @if($mostrarErros)
                            <div class="mt-1 border border-red-200 rounded-lg overflow-hidden divide-y divide-red-100 max-h-48 overflow-y-auto">
                                @foreach($errosPorLinha as $erro)
                                    <div class="px-4 py-2.5 bg-white flex items-start gap-3">
                                        <span class="font-mono text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded flex-shrink-0 mt-0.5">
                                            Linha {{ $erro['linha'] }}
                                        </span>
                                        <ul class="text-xs text-red-600 space-y-0.5">
                                            @foreach($erro['erros'] as $msg)
                                                <li>&mdash; {{ $msg }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Tabela de preview --}}
                <div class="mt-5">
                    <p class="text-xs font-medium text-slate-600 mb-2">
                        Preview &mdash; primeiras {{ count($preview) }} linhas
                        @if($total > 10)<span class="text-slate-400">(de {{ $total }})</span>@endif
                    </p>
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-2 py-2 text-center w-10 font-medium text-slate-500">#</th>
                                    <th class="px-3 py-2 text-center w-16 font-medium text-slate-500">Status</th>
                                    <th class="px-3 py-2 text-left font-medium text-slate-500">Usuario</th>
                                    <th class="px-3 py-2 text-left font-medium text-slate-500">Documento</th>
                                    <th class="px-3 py-2 text-left font-medium text-slate-500">Data</th>
                                    <th class="px-3 py-2 text-right font-medium text-slate-500">Pag.</th>
                                    <th class="px-3 py-2 text-right font-medium text-slate-500">Custo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($preview as $row)
                                    <tr class="{{ $row['_valido'] ? 'hover:bg-slate-50' : 'bg-red-50 hover:bg-red-100' }}">
                                        <td class="px-2 py-2 text-center font-mono text-slate-400">{{ $row['_linha'] }}</td>
                                        <td class="px-3 py-2 text-center">
                                            @if($row['_valido'])
                                                <span class="inline-flex items-center gap-1 text-green-700 font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    OK
                                                </span>
                                            @else
                                                <span
                                                    title="{{ implode(' | ', $row['_erros']) }}"
                                                    class="inline-flex items-center gap-1 text-red-600 font-medium cursor-help"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Erro
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 {{ $row['_valido'] ? 'text-slate-700' : 'text-red-900' }}">
                                            {{ $row['usuario'] ?: '—' }}
                                        </td>
                                        <td class="px-3 py-2 max-w-[180px] {{ $row['_valido'] ? 'text-slate-700' : 'text-red-900' }}">
                                            <span class="truncate block" title="{{ $row['documento'] }}">
                                                {{ $row['documento'] ?: '—' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 font-mono {{ $row['_valido'] ? 'text-slate-500' : 'text-red-700' }}">
                                            {{ $row['data_impressao'] }}
                                        </td>
                                        <td class="px-3 py-2 text-right font-mono {{ $row['_valido'] ? 'text-slate-700' : 'text-red-700' }}">
                                            {{ $row['paginas'] }}
                                        </td>
                                        <td class="px-3 py-2 text-right font-mono {{ $row['_valido'] ? 'text-slate-700' : 'text-red-700' }}">
                                            R$ {{ number_format($row['custo'], 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($invalidos > 0)
                        <p class="text-xs text-slate-400 mt-2 italic">
                            Passe o mouse sobre "Erro" para ver o detalhe de cada linha.
                        </p>
                    @endif
                </div>

                {{-- Acoes --}}
                <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4">
                    <button
                        wire:click="cancelar"
                        class="px-4 py-2 text-sm text-slate-500 hover:text-slate-700 transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        wire:click="importar"
                        @if($validos === 0) disabled @endif
                        class="px-6 py-2 text-sm font-medium rounded-lg transition-colors flex items-center gap-2
                            {{ $validos > 0
                                ? 'bg-primary-900 text-white hover:bg-primary-800'
                                : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Importar {{ $validos }} registro(s) valido(s)
                    </button>
                </div>
            @endif
        @endif

        {{-- Estado: Importando --}}
        @if($status === 'importing')
            <div class="flex flex-col items-center py-10 gap-4">
                <svg class="animate-spin w-10 h-10 text-primary-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-medium text-slate-700">Importando registros...</p>
                <p class="text-xs text-slate-500">Classificando e salvando no banco de dados.</p>
            </div>
        @endif

        {{-- Estado: Sucesso --}}
        @if($status === 'success')
            <div class="flex flex-col items-center py-10 gap-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="text-center">
                    <p class="text-base font-semibold text-slate-800">Importacao concluida!</p>
                    <p class="text-sm text-slate-500 mt-1">
                        <span class="font-mono font-bold text-primary-900">{{ $importados }}</span>
                        registro(s) importados com classificacao automatica.
                    </p>
                    @if($invalidos > 0)
                        <p class="text-xs text-amber-600 mt-1">{{ $invalidos }} linha(s) com erro foram ignoradas.</p>
                    @endif
                </div>
                <div class="flex items-center gap-3 mt-2">
                    <button
                        wire:click="cancelar"
                        class="px-4 py-2 text-sm border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors"
                    >
                        Importar outro arquivo
                    </button>
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 bg-primary-900 text-white text-sm font-medium rounded-lg hover:bg-primary-800 transition-colors">
                        Ver Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
