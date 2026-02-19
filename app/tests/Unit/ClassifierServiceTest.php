<?php

namespace Tests\Unit;

use App\Services\ClassifierService;
use PHPUnit\Framework\TestCase;

class ClassifierServiceTest extends TestCase
{
    private ClassifierService $classifier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->classifier = new ClassifierService();
    }

    // --- CASOS PESSOAL ---

    public function test_boleto_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Boleto Nubank'));
    }

    public function test_fatura_e_classificada_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Fatura Cartao Credito'));
    }

    public function test_curriculo_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Curriculo Vitae'));
    }

    public function test_atestado_medico_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Atestado Medico'));
    }

    public function test_receita_medica_e_classificada_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Receita Medica Dr Silva'));
    }

    public function test_netflix_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Fatura Netflix'));
    }

    public function test_spotify_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Comprovante Spotify'));
    }

    public function test_banco_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Extrato Banco Bradesco'));
    }

    public function test_cpf_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Comprovante CPF'));
    }

    public function test_cnh_e_classificada_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('CNH Renovacao'));
    }

    public function test_amazon_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Pedido Amazon Prime'));
    }

    public function test_mercado_livre_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Compra Mercado Livre'));
    }

    public function test_exame_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('Resultado Exame Sangue'));
    }

    // --- CASOS ADMINISTRATIVO ---

    public function test_ficha_s1_e_classificada_como_administrativo(): void
    {
        $this->assertEquals('ADMINISTRATIVO', $this->classifier->classify('Ficha S1 Caetano'));
    }

    public function test_relatorio_e_classificado_como_administrativo(): void
    {
        $this->assertEquals('ADMINISTRATIVO', $this->classifier->classify('Relatorio Mensal'));
    }

    public function test_oficio_e_classificado_como_administrativo(): void
    {
        $this->assertEquals('ADMINISTRATIVO', $this->classifier->classify('Oficio 001/2025'));
    }

    public function test_memorando_e_classificado_como_administrativo(): void
    {
        $this->assertEquals('ADMINISTRATIVO', $this->classifier->classify('Memorando 123'));
    }

    public function test_portaria_e_classificada_como_administrativo(): void
    {
        $this->assertEquals('ADMINISTRATIVO', $this->classifier->classify('Portaria 45'));
    }

    public function test_escala_e_classificada_como_administrativo(): void
    {
        $this->assertEquals('ADMINISTRATIVO', $this->classifier->classify('Escala de Servico'));
    }

    public function test_documento_sem_keyword_e_administrativo_por_default(): void
    {
        $this->assertEquals('ADMINISTRATIVO', $this->classifier->classify('Documento Desconhecido XYZ'));
    }

    public function test_string_vazia_e_administrativo_por_default(): void
    {
        $this->assertEquals('ADMINISTRATIVO', $this->classifier->classify(''));
    }

    // --- CASE INSENSITIVE ---

    public function test_boleto_maiusculo_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('BOLETO BRADESCO'));
    }

    public function test_boleto_minusculo_e_classificado_como_pessoal(): void
    {
        $this->assertEquals('PESSOAL', $this->classifier->classify('boleto bradesco'));
    }

    // --- CONFIANCA ---

    public function test_match_por_keyword_retorna_confianca_alta(): void
    {
        $result = $this->classifier->classifyWithConfidence('Boleto Nubank');
        $this->assertEquals('PESSOAL', $result['classificacao']);
        $this->assertEquals('ALTA', $result['confianca']);
    }

    public function test_default_sem_match_retorna_confianca_media(): void
    {
        $result = $this->classifier->classifyWithConfidence('Documento Desconhecido');
        $this->assertEquals('ADMINISTRATIVO', $result['classificacao']);
        $this->assertEquals('MEDIA', $result['confianca']);
    }
}
