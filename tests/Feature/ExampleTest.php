<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_successful_response(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Меню', false);
    }

    public function test_menu_page_lists_categories(): void
    {
        $this->seed();

        $response = $this->get('/menu');

        $response->assertStatus(200);
        $response->assertSee('Домашняя еда', false);
    }
}
