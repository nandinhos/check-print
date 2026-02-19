<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_root_redireciona_para_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/dashboard');
    }

    public function test_dashboard_retorna_200(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_pagina_importar_retorna_200(): void
    {
        $response = $this->get('/importar');

        $response->assertStatus(200);
    }
}
