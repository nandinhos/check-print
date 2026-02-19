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
    public string $status = 'idle'; // idle | validating | importing | success | error
    public string $mensagemErro = '';
    public array $preview = [];
    public bool $mostrarPreview = false;

    public function updatedFile(): void
    {
        $this->validate();
        $this->prepararPreview();
    }

    private function prepararPreview(): void
    {
        if (! $this->file) {
            return;
        }

        $parser = new CsvParserService();
        $content = file_get_contents($this->file->getRealPath());

        if (! $parser->validateHeader($content)) {
            $this->status = 'error';
            $this->mensagemErro = 'Cabecalho invalido. O CSV deve conter: Data, Hora, Usuario, Documento, Paginas, Custo.';
            return;
        }

        $rows = $parser->parse($content);
        $this->total = count($rows);
        $this->preview = array_slice($rows, 0, 5);
        $this->mostrarPreview = true;
        $this->status = 'validating';
    }

    public function importar(): void
    {
        if (! $this->file || $this->status === 'error') {
            return;
        }

        $this->status = 'importing';
        $this->importados = 0;

        $parser = new CsvParserService();
        $classifier = new ClassifierService();
        $content = file_get_contents($this->file->getRealPath());
        $rows = $parser->parse($content);

        DB::transaction(function () use ($rows, $classifier) {
            foreach ($rows as $row) {
                if (empty($row['usuario']) || empty($row['documento'])) {
                    continue;
                }

                $resultado = $classifier->classifyWithConfidence($row['documento']);

                PrintLog::create([
                    'usuario'           => $row['usuario'],
                    'documento'         => $row['documento'],
                    'data_impressao'    => $row['data_impressao'],
                    'paginas'           => $row['paginas'],
                    'custo'             => $row['custo'],
                    'aplicativo'        => $row['aplicativo'],
                    'classificacao'     => $resultado['classificacao'],
                    'classificacao_origem' => 'AUTO',
                ]);

                $this->importados++;
            }
        });

        $this->status = 'success';
        $this->file = null;
        $this->mostrarPreview = false;
        $this->dispatch('csv-importado', total: $this->importados);
    }

    public function cancelar(): void
    {
        $this->reset(['file', 'status', 'mensagemErro', 'preview', 'mostrarPreview', 'importados', 'total']);
        $this->status = 'idle';
    }

    public function render()
    {
        return view('livewire.import-csv');
    }
}
