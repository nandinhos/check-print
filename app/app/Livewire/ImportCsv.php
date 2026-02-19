<?php

namespace App\Livewire;

use App\Models\PrintLog;
use App\Services\ClassifierService;
use App\Services\CsvParserService;
use App\Services\DuplicataService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportCsv extends Component
{
    use WithFileUploads;

    #[Validate(['file' => 'required|file|mimes:csv,txt|max:51200'])]
    public $file = null;

    public int $importados   = 0;
    public int $ignorados    = 0; // linhas invalidas (formato)
    public int $duplicatas   = 0; // linhas ja existentes no banco
    public int $total        = 0;
    public int $validos      = 0;
    public int $invalidos    = 0;

    // 'idle' | 'validating' | 'importing' | 'success' | 'error'
    public string $status       = 'idle';
    public string $mensagemErro = '';

    public array $preview          = [];
    public array $errosPorLinha    = [];
    public array $duplicatasPorLinha = [];
    public bool  $mostrarPreview   = false;
    public bool  $mostrarErros     = false;
    public bool  $mostrarDuplicatas = false;

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

        $headerInfo = $parser->validateHeaderDetail($content);
        if (! $headerInfo['valid']) {
            $this->status       = 'error';
            $this->mensagemErro = 'Cabecalho invalido. Colunas faltando: ' . implode(', ', $headerInfo['missing']) . '. '
                . 'Use o modelo disponivel para download.';
            return;
        }

        $result = $parser->parseWithValidation($content);

        $this->total     = $result['total'];
        $this->invalidos = $result['invalidos'];
        $this->errosPorLinha = $result['errors'];

        // Verificar duplicatas apenas nas linhas validas
        $duplicataService = new DuplicataService();
        $validasRows      = array_filter($result['rows'], fn ($r) => $r['_valido']);
        $verificado       = $duplicataService->verificarLote(array_values($validasRows));

        $this->validos             = count($verificado['novos']);
        $this->duplicatas          = count($verificado['duplicatas']);
        $this->duplicatasPorLinha  = $verificado['duplicatas'];
        $this->mostrarDuplicatas   = $this->duplicatas > 0;

        // Preview: primeiras 10 linhas (todas, com e sem erro)
        $this->preview = array_slice($result['rows'], 0, 10);

        // Marca no preview quais sao duplicatas
        $linhasDuplicatas = array_column($verificado['duplicatas'], 'linha');
        foreach ($this->preview as &$row) {
            $row['_duplicata'] = in_array($row['_linha'], $linhasDuplicatas);
        }
        unset($row);

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
        $this->ignorados  = 0;
        $this->duplicatas = 0;

        $parser           = new CsvParserService();
        $classifier       = new ClassifierService();
        $duplicataService = new DuplicataService();
        $content          = file_get_contents($this->file->getRealPath());
        $result           = $parser->parseWithValidation($content);

        $validasRows = array_filter($result['rows'], fn ($r) => $r['_valido']);
        $verificado  = $duplicataService->verificarLote(array_values($validasRows));

        $this->ignorados  = $result['invalidos'];
        $this->duplicatas = count($verificado['duplicatas']);

        DB::transaction(function () use ($verificado, $classifier) {
            foreach ($verificado['novos'] as $row) {
                $resultado = $classifier->classifyWithConfidence($row['documento']);

                PrintLog::create([
                    'usuario'              => $row['usuario'],
                    'documento'            => $row['documento'],
                    'data_impressao'       => $row['data_impressao'],
                    'paginas'              => $row['paginas'],
                    'custo'                => $row['custo'],
                    'aplicativo'           => $row['aplicativo'],
                    'classificacao'        => $resultado['classificacao'],
                    'classificacao_auto'   => $resultado['classificacao'],
                    'classificacao_origem' => 'AUTO',
                ]);

                $this->importados++;
            }
        });

        $this->status         = 'success';
        $this->file           = null;
        $this->mostrarPreview = false;
        $this->dispatch('csv-importado', total: $this->importados);
    }

    public function cancelar(): void
    {
        $this->reset([
            'file', 'status', 'mensagemErro', 'preview',
            'mostrarPreview', 'importados', 'total', 'validos',
            'invalidos', 'errosPorLinha', 'mostrarErros',
            'duplicatas', 'duplicatasPorLinha', 'mostrarDuplicatas',
            'ignorados',
        ]);
        $this->status = 'idle';
    }

    public function render()
    {
        return view('livewire.import-csv');
    }
}
