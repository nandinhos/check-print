<?php

namespace App\Livewire;

use App\Models\PrintLog;
use App\Services\ClassifierService;
use App\Services\CsvParserService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportCsv extends Component
{
    use WithFileUploads;

    #[Validate(['file' => 'required|file|mimes:csv,txt|max:51200'])]
    public $file = null;

    public int $importados = 0;
    public int $total = 0;
    public int $validos = 0;
    public int $invalidos = 0;

    // 'idle' | 'validating' | 'importing' | 'success' | 'error'
    public string $status = 'idle';
    public string $mensagemErro = '';

    // Preview: primeiras 10 linhas com status de validacao
    public array $preview = [];
    // Erros por linha: [{linha, erros[]}]
    public array $errosPorLinha = [];
    public bool $mostrarPreview = false;
    // Expandir painel de erros
    public bool $mostrarErros = false;

    public function updatedFile(): void
    {
        $this->validate();
        $this->analisarArquivo();
    }

    private function analisarArquivo(): void
    {
        if (! $this->file) {
            return;
        }

        $parser  = new CsvParserService();
        $content = file_get_contents($this->file->getRealPath());

        // 1. Validar cabecalho
        $headerInfo = $parser->validateHeaderDetail($content);
        if (! $headerInfo['valid']) {
            $this->status      = 'error';
            $this->mensagemErro = 'Cabecalho invalido. Colunas faltando: ' . implode(', ', $headerInfo['missing']) . '. '
                . 'Use o modelo disponivel para download.';
            return;
        }

        // 2. Parsear com validacao linha a linha
        $result = $parser->parseWithValidation($content);

        $this->total         = $result['total'];
        $this->validos       = $result['validos'];
        $this->invalidos     = $result['invalidos'];
        $this->errosPorLinha = $result['errors'];

        // Preview: primeiras 10 linhas (com e sem erro)
        $this->preview = array_slice($result['rows'], 0, 10);

        $this->mostrarPreview = true;
        $this->mostrarErros   = $this->invalidos > 0;
        $this->status         = 'validating';
    }

    public function importar(): void
    {
        if (! $this->file || $this->status === 'error') {
            return;
        }

        $this->status     = 'importing';
        $this->importados = 0;

        $parser     = new CsvParserService();
        $classifier = new ClassifierService();
        $content    = file_get_contents($this->file->getRealPath());
        $result     = $parser->parseWithValidation($content);

        DB::transaction(function () use ($result, $classifier) {
            foreach ($result['rows'] as $row) {
                // Pular linhas invalidas
                if (! $row['_valido']) {
                    continue;
                }

                $resultado = $classifier->classifyWithConfidence($row['documento']);

                PrintLog::create([
                    'usuario'              => $row['usuario'],
                    'documento'            => $row['documento'],
                    'data_impressao'       => $row['data_impressao'],
                    'paginas'              => $row['paginas'],
                    'custo'                => $row['custo'],
                    'aplicativo'           => $row['aplicativo'],
                    'classificacao'        => $resultado['classificacao'],
                    'classificacao_origem' => 'AUTO',
                ]);

                $this->importados++;
            }
        });

        $this->status = 'success';
        $this->file   = null;
        $this->mostrarPreview = false;
        $this->dispatch('csv-importado', total: $this->importados);
    }

    public function cancelar(): void
    {
        $this->reset([
            'file', 'status', 'mensagemErro', 'preview',
            'mostrarPreview', 'importados', 'total', 'validos',
            'invalidos', 'errosPorLinha', 'mostrarErros',
        ]);
        $this->status = 'idle';
    }

    public function render()
    {
        return view('livewire.import-csv');
    }
}
