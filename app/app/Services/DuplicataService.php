<?php

namespace App\Services;

use App\Models\PrintLog;
use Illuminate\Support\Collection;

class DuplicataService
{
    /**
     * Verifica se uma linha do CSV ja existe no banco.
     * Chave de negocio: usuario + documento + data_impressao + paginas
     */
    public function isDuplicata(array $row): bool
    {
        return PrintLog::where('usuario', $row['usuario'])
            ->where('documento', $row['documento'])
            ->where('data_impressao', $row['data_impressao'])
            ->where('paginas', $row['paginas'])
            ->exists();
    }

    /**
     * Verifica um lote de linhas contra o banco e contra si mesmo (duplicatas internas).
     * Duplicatas cujo fingerprint esteja em $aprovadas sao tratadas como novos registros.
     *
     * @param  array<int, array<string, mixed>> $rows      Linhas ja validadas (_valido = true)
     * @param  array<string>                    $aprovadas Fingerprints aprovados pelo usuario
     * @return array{novos: array, duplicatas: array}
     */
    public function verificarLote(array $rows, array $aprovadas = []): array
    {
        $novos      = [];
        $duplicatas = [];

        // Fingerprints ja vistos neste lote (previne duplicatas internas no CSV)
        $vistos = [];

        foreach ($rows as $row) {
            $fp = $this->fingerprint($row);

            $duplicataInterna = isset($vistos[$fp]);
            $duplicataDB      = ! $duplicataInterna && $this->isDuplicata($row);

            if (($duplicataInterna || $duplicataDB) && ! in_array($fp, $aprovadas, true)) {
                $duplicatas[] = [
                    'linha'       => $row['_linha'],
                    'usuario'     => $row['usuario'],
                    'documento'   => $row['documento'],
                    'data'        => $row['data_impressao'],
                    'paginas'     => $row['paginas'],
                    'origem'      => $duplicataDB ? 'banco' : 'arquivo',
                    'fingerprint' => $fp,
                ];
            } else {
                $vistos[$fp] = true;
                $novos[]     = $row;
            }
        }

        return [
            'novos'      => $novos,
            'duplicatas' => $duplicatas,
        ];
    }

    private function fingerprint(array $row): string
    {
        return implode('|', [
            mb_strtolower(trim($row['usuario'])),
            mb_strtolower(trim($row['documento'])),
            $row['data_impressao'],
            (int) $row['paginas'],
        ]);
    }
}
