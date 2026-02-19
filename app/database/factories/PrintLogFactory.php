<?php

namespace Database\Factories;

use App\Models\PrintLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrintLogFactory extends Factory
{
    protected $model = PrintLog::class;

    private static array $documentosPessoais = [
        'Boleto Nubank',
        'Fatura Cartao',
        'Curriculo Vitae',
        'Atestado Medico',
        'Receita Medica',
        'CPF Declaracao',
        'Boleto Banco',
        'Fatura Netflix',
        'Comprovante Amazon',
    ];

    private static array $documentosAdministrativos = [
        'Ficha S1 Caetano',
        'Relatorio Mensal',
        'Oficio 001/2025',
        'Memorando 123',
        'Portaria 45',
        'Escala de Servico',
        'Boletim Interno',
        'Ata de Reuniao',
        'Ordem do Dia',
        'Instrucao Normativa',
    ];

    public function definition(): array
    {
        $isPessoal = $this->faker->boolean(30);
        $documentos = $isPessoal
            ? self::$documentosPessoais
            : self::$documentosAdministrativos;

        return [
            'usuario' => $this->faker->name(),
            'documento' => $this->faker->randomElement($documentos),
            'data_impressao' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'paginas' => $this->faker->numberBetween(1, 20),
            'custo' => $this->faker->randomFloat(4, 0.02, 5.00),
            'aplicativo' => $this->faker->randomElement(['PDF', 'Chrome', 'Word', 'Excel', 'Firefox']),
            'classificacao' => $isPessoal ? 'PESSOAL' : 'ADMINISTRATIVO',
            'classificacao_auto' => $isPessoal ? 'PESSOAL' : 'ADMINISTRATIVO',
            'classificacao_origem' => 'AUTO',
        ];
    }

    public function pessoal(): static
    {
        return $this->state(fn () => [
            'documento' => $this->faker->randomElement(self::$documentosPessoais),
            'classificacao' => 'PESSOAL',
        ]);
    }

    public function administrativo(): static
    {
        return $this->state(fn () => [
            'documento' => $this->faker->randomElement(self::$documentosAdministrativos),
            'classificacao' => 'ADMINISTRATIVO',
        ]);
    }
}
