<?php

namespace App\Services;

class ClassifierService
{
    private const KEYWORDS_PESSOAL = [
        // Financeiro pessoal
        'boleto',
        'fatura',
        'banco',
        'extrato',
        'financiamento',
        'emprestimo',
        // Servicos de streaming / e-commerce
        'netflix',
        'spotify',
        'amazon',
        'mercado livre',
        'shopee',
        'aliexpress',
        'nubank',
        // Documentos pessoais
        'curriculo',
        'curriculum',
        'atestado',
        'receita',
        'exame',
        'laudo',
        'cpf',
        'cnh',
        'rg',
        'identidade',
        'passaporte',
        // Outros sinais pessoais
        'comprovante de residencia',
        'declaracao ir',
        'imposto de renda',
    ];

    /**
     * Classifica um documento entre PESSOAL e ADMINISTRATIVO.
     * Retorna ADMINISTRATIVO por padrao (fallback seguro).
     */
    public function classify(string $documento): string
    {
        return $this->classifyWithConfidence($documento)['classificacao'];
    }

    /**
     * Classifica e retorna tambem o nivel de confianca.
     *
     * @return array{classificacao: string, confianca: string}
     */
    public function classifyWithConfidence(string $documento): array
    {
        $documentoLower = mb_strtolower($documento);

        foreach (self::KEYWORDS_PESSOAL as $keyword) {
            if (str_contains($documentoLower, $keyword)) {
                return [
                    'classificacao' => 'PESSOAL',
                    'confianca' => 'ALTA',
                ];
            }
        }

        return [
            'classificacao' => 'ADMINISTRATIVO',
            'confianca' => 'MEDIA',
        ];
    }
}
