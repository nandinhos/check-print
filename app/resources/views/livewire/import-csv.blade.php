<div class="max-w-2xl">

    {{-- Card de Upload --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <div class="mb-6">
            <h3 class="text-base font-semibold text-slate-800">Importar Arquivo CSV</h3>
            <p class="text-sm text-slate-500 mt-1">
                O arquivo deve conter as colunas: Data, Hora, Usuario, Documento, Paginas, Custo.
            </p>
        </div>

        {{-- Estado: idle ou validating - mostrar upload --}}
        @if(in_array($status, ['idle', 'validating', 'error']))

            {{-- Dropzone --}}
            <div
                x-data="{ dragging: false }"
                @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false"
                @drop.prevent="dragging = false"
                :class="dragging ? 'border-primary-500 bg-primary-50' : 'border-slate-300 hover:border-primary-400'"
                class="border-2 border-dashed rounded-xl p-10 text-center transition-colors cursor-pointer"
                onclick="document.getElementById('csv-input').click()"
            >
                <input
                    id="csv-input"
                    type="file"
                    accept=".csv,.txt"
                    class="hidden"
                    wire:model="file"
                    @change="dragging = false"
                >

                <div class="flex flex-col items-center gap-3">
                    <div class="w-14 h-14 bg-primary-50 rounded-full flex items-center justify-center">
                        <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-700">Arraste ou Clique para enviar CSV</p>
                        <p class="text-xs text-slate-400 mt-1">Formato: .csv ou .txt &mdash; Separador: ; ou ,</p>
                    </div>
                </div>

                {{-- Loading spinner durante upload --}}
                <div wire:loading wire:target="file" class="mt-4">
                    <div class="flex items-center justify-center gap-2 text-primary-600">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm">Validando arquivo...</span>
                    </div>
                </div>
            </div>

            {{-- Erro de validacao --}}
            @if($status === 'error')
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-700">Erro no arquivo</p>
                        <p class="text-sm text-red-600 mt-0.5">{{ $mensagemErro }}</p>
                    </div>
                </div>
            @endif

            {{-- Erros de validacao do Livewire --}}
            @error('file')
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ $message }}</p>
                </div>
            @enderror

            {{-- Preview das primeiras 5 linhas --}}
            @if($mostrarPreview && count($preview) > 0)
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-slate-700">
                            Preview (primeiras {{ count($preview) }} de {{ $total }} registros)
                        </h4>
                        <span class="text-xs text-slate-400">{{ $total }} registros detectados</span>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-3 py-2 text-left font-medium text-slate-600">Usuario</th>
                                    <th class="px-3 py-2 text-left font-medium text-slate-600">Documento</th>
                                    <th class="px-3 py-2 text-left font-medium text-slate-600">Data</th>
                                    <th class="px-3 py-2 text-right font-medium text-slate-600">Pag.</th>
                                    <th class="px-3 py-2 text-right font-medium text-slate-600">Custo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($preview as $row)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-3 py-2 text-slate-700">{{ $row['usuario'] }}</td>
                                        <td class="px-3 py-2 text-slate-700 max-w-[200px] truncate">{{ $row['documento'] }}</td>
                                        <td class="px-3 py-2 text-slate-500 font-mono">{{ $row['data_impressao'] }}</td>
                                        <td class="px-3 py-2 text-slate-700 text-right font-mono">{{ $row['paginas'] }}</td>
                                        <td class="px-3 py-2 text-slate-700 text-right font-mono">R$ {{ number_format($row['custo'], 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <button
                            wire:click="cancelar"
                            class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button
                            wire:click="importar"
                            class="px-6 py-2 bg-primary-900 text-white text-sm font-medium rounded-lg hover:bg-primary-800 transition-colors flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Importar {{ $total }} registros
                        </button>
                    </div>
                </div>
            @endif
        @endif

        {{-- Estado: Importando --}}
        @if($status === 'importing')
            <div class="flex flex-col items-center py-8 gap-4">
                <svg class="animate-spin w-10 h-10 text-primary-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-medium text-slate-700">Importando registros...</p>
                <p class="text-xs text-slate-500">Aguarde, o processo pode levar alguns segundos.</p>
            </div>
        @endif

        {{-- Estado: Sucesso --}}
        @if($status === 'success')
            <div class="flex flex-col items-center py-8 gap-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="text-center">
                    <p class="text-base font-semibold text-slate-800">Importacao concluida!</p>
                    <p class="text-sm text-slate-500 mt-1">
                        <span class="font-mono font-semibold text-primary-900">{{ $importados }}</span> registros importados com sucesso.
                    </p>
                </div>
                <div class="flex items-center gap-3 mt-2">
                    <button
                        wire:click="cancelar"
                        class="px-4 py-2 text-sm border border-slate-300 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors"
                    >
                        Importar outro arquivo
                    </button>
                    <a
                        href="{{ route('dashboard') }}"
                        class="px-4 py-2 bg-primary-900 text-white text-sm font-medium rounded-lg hover:bg-primary-800 transition-colors"
                    >
                        Ver Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
