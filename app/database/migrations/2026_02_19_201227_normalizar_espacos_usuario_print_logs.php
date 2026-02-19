<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Insere espaço entre graduação e nome quando estão colados em CamelCase.
     * Ex: "1SFernandoSouza" → "1S Fernando Souza"
     *     "CapCheyla"       → "Cap Cheyla"
     *     "S1RVieira"       → "S1 R Vieira"
     */
    public function up(): void
    {
        $nomes = DB::table('print_logs')->distinct()->pluck('usuario');

        foreach ($nomes as $nome) {
            $corrigido = $this->normalizar($nome);
            if ($corrigido !== $nome) {
                DB::table('print_logs')
                    ->where('usuario', $nome)
                    ->update(['usuario' => $corrigido]);
            }
        }
    }

    public function down(): void
    {
        // Não é possível reverter normalização de strings sem backup prévio.
    }

    private function normalizar(string $nome): string
    {
        // Graduações conhecidas: numéricas (1S, 2T…) e alfabéticas (Cap, TC…)
        $graduacoes = '1S|1T|2S|2T|3S|3T|S1|S2|SO|Cap|Cel|Maj|TC|Ten|Sgt|Cb|Sd';

        // Passo 1: insere espaço após a graduação se seguida diretamente de letra maiúscula
        // "1SBrasil" → "1S Brasil" | "CapCheyla" → "Cap Cheyla"
        $nome = preg_replace(
            '/^(' . $graduacoes . ')([A-ZÁÉÍÓÚÃÕÂÊÎÔÛÀÈÌÒÙÇÜ])/u',
            '$1 $2',
            $nome
        );

        // Passo 2: separa sequência de maiúsculas antes de palavra com minúsculas
        // "S1 RVieira" → "S1 R Vieira" | "S2 PHenrique" → "S2 P Henrique"
        $nome = preg_replace(
            '/([A-ZÁÉÍÓÚÃÕÂÊÎÔÛÀÈÌÒÙÇÜ])([A-ZÁÉÍÓÚÃÕÂÊÎÔÛÀÈÌÒÙÇÜ][a-záéíóúãõâêîôûàèìòùçü])/u',
            '$1 $2',
            $nome
        );

        // Passo 3: insere espaço entre letra minúscula e maiúscula seguinte
        // "FernandoSouza" → "Fernando Souza" | "ElainePedrosoMacedo" → "Elaine Pedroso Macedo"
        $nome = preg_replace(
            '/([a-záéíóúãõâêîôûàèìòùçü])([A-ZÁÉÍÓÚÃÕÂÊÎÔÛÀÈÌÒÙÇÜ])/u',
            '$1 $2',
            $nome
        );

        return $nome;
    }
};
