<?php

namespace App\Console\Commands;

use App\Models\PrintLog;
use App\Services\ClassifierService;
use Illuminate\Console\Command;

class ReclassifyPrintLogs extends Command
{
    /**
     * O nome e a assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'app:reclassify-logs {--dry-run : Apenas simula as alteracoes sem salvar no banco}';

    /**
     * A descricao do comando.
     *
     * @var string
     */
    protected $description = 'Reclassifica todos os logs de impressao baseando-se na heuristica atual do ClassifierService';

    /**
     * Executa o comando.
     */
    public function handle(ClassifierService $classifier): int
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('MODO SIMULACAO (DRY RUN) ATIVADO - Nenhuma alteracao sera salva.');
        }

        $total = PrintLog::count();
        $atualizados = 0;
        $processados = 0;

        $this->info("Iniciando processamento de {$total} registros...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // Usamos chunk para evitar estourar a memoria em bases muito grandes
        PrintLog::chunk(200, function ($logs) use ($classifier, $dryRun, &$atualizados, &$processados, $bar) {
            foreach ($logs as $log) {
                $novaClassificacao = $classifier->classify($log->documento);
                
                if ($log->classificacao !== $novaClassificacao) {
                    if (!$dryRun) {
                        $log->classificacao = $novaClassificacao;
                        $log->save();
                    }
                    $atualizados++;
                }
                
                $processados++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $status = $dryRun ? 'seriam atualizados' : 'foram atualizados';
        $this->table(
            ['Descricao', 'Quantidade'],
            [
                ['Total Processados', $processados],
                ["Registros que {$status}", $atualizados],
                ['Permaneceram iguais', $processados - $atualizados]
            ]
        );

        $this->info('Operacao concluida com sucesso!');

        return self::SUCCESS;
    }
}
