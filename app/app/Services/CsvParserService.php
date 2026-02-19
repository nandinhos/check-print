<?php

namespace App\Services;

class CsvParserService
{
    private const REQUIRED_COLUMNS = ['usuario', 'documento', 'data', 'paginas', 'custo'];

    private const COLUMN_MAP = [
        'usuario'    => 'usuario',
        'user'       => 'usuario',
        'documento'  => 'documento',
        'document'   => 'documento',
        'nome do documento' => 'documento',
        'data'       => 'data',
        'hora'       => 'hora',
        'paginas'    => 'paginas',
        'pages'      => 'paginas',
        'custo'      => 'custo',
        'cost'       => 'custo',
        'aplicativo' => 'aplicativo',
        'app'        => 'aplicativo',
    ];

    /**
     * Valida se o cabecalho do CSV contem as colunas minimas necessarias.
     */
    public function validateHeader(string $content): bool
    {
        $lines = $this->splitLines($content);

        if (empty($lines)) {
            return false;
        }

        $separator = $this->detectSeparator($lines[0]);
        $headers = $this->normalizeHeaders(str_getcsv($lines[0], $separator, '"', ''));

        foreach (self::REQUIRED_COLUMNS as $required) {
            if (! in_array($required, $headers, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Faz o parsing do conteudo CSV e retorna array de linhas normalizadas.
     *
     * @return array<int, array<string, mixed>>
     */
    public function parse(string $content): array
    {
        $lines = $this->splitLines($content);

        if (count($lines) < 2) {
            return [];
        }

        $separator = $this->detectSeparator($lines[0]);
        $headers = $this->normalizeHeaders(str_getcsv($lines[0], $separator, '"', ''));

        $rows = [];
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);

            if ($line === '') {
                continue;
            }

            $values = str_getcsv($line, $separator, '"', '');
            $row = array_combine($headers, array_pad($values, count($headers), ''));

            $rows[] = $this->normalizeRow($row);
        }

        return $rows;
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
            'usuario'       => trim($row['usuario'] ?? ''),
            'documento'     => trim($row['documento'] ?? ''),
            'data_impressao' => $this->parseDateTime($data, $hora),
            'paginas'       => (int) ($row['paginas'] ?? 1),
            'custo'         => $this->parseCusto($row['custo'] ?? '0'),
            'aplicativo'    => trim($row['aplicativo'] ?? ''),
        ];
    }

    private function parseDateTime(string $data, string $hora): string
    {
        // Formato esperado: DD/MM/YYYY
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
        // Suporta virgula decimal: "1,50" -> 1.50
        $normalized = str_replace(',', '.', trim($custo));
        return (float) $normalized;
    }
}
