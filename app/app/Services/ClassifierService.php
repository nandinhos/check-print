<?php

namespace App\Services;

class ClassifierService
{
    private const KEYWORDS_ADMINISTRATIVO_FORTE = [
        // Projetos estrategicos GAC-PAC
        'kc-390', 'kc-x', 'link-br2', 'am-x', 'f5-br', 'hx-br', 'th-x', 'fx-32',
        'projeto radar', 'projeto sivam', 'projeto mar', 'projeto',
        // Documentos Oficiais / Fiscais / Patrimoniais
        'nota fiscal', ' nf', 'danfe', 'guia de remessa', 'ordem de servico',
        'oficio', 'memorando', 'portaria', 'escala de servico', 'contrato',
        'cautela', 'termo de', 'notebook', 'laptop', 'computador',
        'laudo tecnico', 'laudo de ti', 'laudo de informatica',
    ];

    private const KEYWORDS_PESSOAL = [
        // Financeiro pessoal
        'boleto', 'fatura', 'banco', 'extrato', 'financiamento', 'emprestimo',
        // Servicos de streaming / lazer / compras
        'netflix', 'spotify', 'amazon', 'mercado livre', 'shopee', 'aliexpress', 'nubank', 'e-book', 'livro',
        // Saude e Documentos pessoais (Alta Sensibilidade)
        'curriculo', 'curriculum', 'atestado', 'receita', 'prescricao', 'laudo', 'biopsia', 'exame', 'tomografia', 'ressonancia',
        'medico', 'medica', 'odontologico', 'clinica', 'hospital', 'saude',
        'cpf', 'cnh', 'rg', 'identidade', 'passaporte',
        // Outros sinais pessoais
        'comprovante de residencia', 'declaracao ir', 'imposto de renda',
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
        // Normalizacao: remover acentos e espacos extras
        $documentoLower = mb_strtolower($this->removerAcentos(trim($documento)));

        // 1. Veto Administrativo Forte (Prioridade Maxima)
        foreach (self::KEYWORDS_ADMINISTRATIVO_FORTE as $keyword) {
            if (str_contains($documentoLower, $keyword)) {
                return [
                    'classificacao' => 'ADMINISTRATIVO',
                    'confianca' => 'MUITO ALTA',
                ];
            }
        }

        // 2. Identificacao Pessoal
        foreach (self::KEYWORDS_PESSOAL as $keyword) {
            if (str_contains($documentoLower, $keyword)) {
                return [
                    'classificacao' => 'PESSOAL',
                    'confianca' => 'ALTA',
                ];
            }
        }

        // 3. Fallback Seguro
        return [
            'classificacao' => 'ADMINISTRATIVO',
            'confianca' => 'MEDIA',
        ];
    }

    private function removerAcentos(string $string): string
    {
        return preg_replace(
            ['/[áàâãä]/u', '/[éèêë]/u', '/[íìîï]/u', '/[óòôõö]/u', '/[úùûü]/u', '/[ç]/u'],
            ['a', 'e', 'i', 'o', 'u', 'c'],
            $string
        );
    }
}
