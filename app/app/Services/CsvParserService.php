<?php

namespace App\Services;

class CsvParserService
{
    private const REQUIRED_COLUMNS = ['usuario', 'documento', 'data', 'paginas', 'custo'];

    private const COLUMN_MAP = [
        'usuario'           => 'usuario',
        'user'              => 'usuario',
        'documento'         => 'documento',
        'document'          => 'documento',
        'nome do documento' => 'documento',
        'data'              => 'data',
        'hora'              => 'hora',
        'paginas'           => 'paginas',
        'pages'             => 'paginas',
        'custo'             => 'custo',
        'cost'              => 'custo',
        'aplicativo'        => 'aplicativo',
        'app'               => 'aplicativo',
    ];

    /**
     * Valida se o cabecalho do CSV contem as colunas minimas necessarias.
     * Retorna array ['valid' => bool, 'missing' => string[], 'found' => string[]]
     *
     * @return array{valid: bool, missing: string[], found: string[]}
     */
    public function validateHeader(string $content): bool
    {
        return $this->validateHeaderDetail($content)['valid'];
    }

    /**
     * @return array{valid: bool, missing: string[], found: string[], separator: string}
     */
    public function validateHeaderDetail(string $content): array
    {
        $lines = $this->splitLines($content);

        if (empty($lines)) {
            return ['valid' => false, 'missing' => self::REQUIRED_COLUMNS, 'found' => [], 'separator' => ';'];
        }

        $separator = $this->detectSeparator($lines[0]);
        $headers   = $this->normalizeHeaders(str_getcsv($lines[0], $separator, '"', ''));
        $missing   = array_values(array_diff(self::REQUIRED_COLUMNS, $headers));

        return [
            'valid'     => empty($missing),
            'missing'   => $missing,
            'found'     => $headers,
            'separator' => $separator,
        ];
    }

    /**
     * Faz o parsing do conteudo CSV e retorna linhas normalizadas com status de validacao.
     *
     * @return array{
     *   rows: array<int, array<string, mixed>>,
     *   errors: array<int, array{linha: int, erros: string[]}>,
     *   total: int,
     *   validos: int,
     *   invalidos: int
     * }
     */
    public function parseWithValidation(string $content): array
    {
        $lines = $this->splitLines($content);

        if (count($lines) < 2) {
            return ['rows' => [], 'errors' => [], 'total' => 0, 'validos' => 0, 'invalidos' => 0];
        }

        $separator = $this->detectSeparator($lines[0]);
        $headers   = $this->normalizeHeaders(str_getcsv($lines[0], $separator, '"', ''));

        $rows    = [];
        $errors  = [];
        $lineNum = 1; // linha 1 = cabecalho

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            $lineNum++;

            if ($line === '') {
                continue;
            }

            $values   = str_getcsv($line, $separator, '"', '');
            $raw      = array_combine($headers, array_pad($values, count($headers), ''));
            $rowErrors = $this->validateRow($raw, $lineNum);

            $normalized = $this->normalizeRow($raw);
            $normalized['_linha']  = $lineNum;
            $normalized['_valido'] = empty($rowErrors);
            $normalized['_erros']  = $rowErrors;

            $rows[] = $normalized;

            if (! empty($rowErrors)) {
                $errors[] = ['linha' => $lineNum, 'erros' => $rowErrors];
            }
        }

        $invalidos = count($errors);

        return [
            'rows'      => $rows,
            'errors'    => $errors,
            'total'     => count($rows),
            'validos'   => count($rows) - $invalidos,
            'invalidos' => $invalidos,
        ];
    }

    /**
     * Parsing simples — compatibilidade com codigo existente.
     *
     * @return array<int, array<string, mixed>>
     */
    public function parse(string $content): array
    {
        $result = $this->parseWithValidation($content);
        return array_map(
            fn ($row) => array_filter($row, fn ($k) => ! str_starts_with($k, '_'), ARRAY_FILTER_USE_KEY),
            $result['rows']
        );
    }

    // --- privados ---

    private function validateRow(array $raw, int $lineNum): array
    {
        $erros = [];

        if (empty(trim($raw['usuario'] ?? ''))) {
            $erros[] = 'Usuario vazio';
        }

        if (empty(trim($raw['documento'] ?? ''))) {
            $erros[] = 'Documento vazio';
        }

        $data = trim($raw['data'] ?? '');
        if (! preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data)) {
            $erros[] = "Data invalida: \"{$data}\" (esperado DD/MM/AAAA)";
        }

        $paginas = trim($raw['paginas'] ?? '');
        if (! is_numeric($paginas) || (int) $paginas < 1) {
            $erros[] = "Paginas invalido: \"{$paginas}\"";
        }

        $custo = str_replace(',', '.', trim($raw['custo'] ?? ''));
        if (! is_numeric($custo)) {
            $erros[] = "Custo invalido: \"{$raw['custo']}\"";
        }

        return $erros;
    }

    private function splitLines(string $content): array
    {
        return explode("\n", str_replace("\r\n", "\n", trim($content)));
    }

    private function detectSeparator(string $headerLine): string
    {
        return substr_count($headerLine, ';') >= substr_count($headerLine, ',') ? ';' : ',';
    }

    private function normalizeHeaders(array $headers): array
    {
        return array_map(function (string $header): string {
            $normalized = mb_strtolower(trim($header));
            return self::COLUMN_MAP[$normalized] ?? $normalized;
        }, $headers);
    }

    private function normalizeRow(array $row): array
    {
        $data = $row['data'] ?? '';
        $hora = $row['hora'] ?? '00:00:00';

        return [
            'usuario'        => trim($row['usuario'] ?? ''),
            'documento'      => trim($row['documento'] ?? ''),
            'data_impressao' => $this->parseDateTime($data, $hora),
            'paginas'        => max(1, (int) ($row['paginas'] ?? 1)),
            'custo'          => $this->parseCusto($row['custo'] ?? '0'),
            'aplicativo'     => trim($row['aplicativo'] ?? ''),
        ];
    }

    private function parseDateTime(string $data, string $hora): string
    {
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $data, $m)) {
            $dateFormatted = "{$m[3]}-{$m[2]}-{$m[1]}";
            $hora = preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $hora) ? $hora : '00:00:00';

            if (strlen($hora) === 5) {
                $hora .= ':00';
            }

            return "{$dateFormatted} {$hora}";
        }

        return now()->format('Y-m-d H:i:s');
    }

    private function parseCusto(string $custo): float
    {
        return (float) str_replace(',', '.', trim($custo));
    }
}
