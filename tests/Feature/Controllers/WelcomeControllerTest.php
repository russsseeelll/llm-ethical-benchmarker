<?php

namespace Tests\Feature\Controllers;

use App\Models\Persona;
use App\Models\Scenario;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class WelcomeControllerTest extends TestCase
{
    use DatabaseMigrations;
    public function test_index_displays_welcome_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }

    public function test_index_loads_personas_and_scenarios(): void
    {
        $persona = Persona::factory()->create(['name' => 'Test Persona']);
        $scenario = Scenario::factory()->create(['title' => 'Test Scenario']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('personas');
        $response->assertViewHas('scenarios');
        $response->assertSee('Test Persona');
        $response->assertSee('Test Scenario');
    }

    public function test_index_paginates_personas(): void
    {
        // Create more than 10 personas to test pagination
        Persona::factory()->count(15)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('personas');
        
        $personas = $response->viewData('personas');
        $this->assertEquals(3, $personas->count()); // Default pagination
    }

    public function test_index_paginates_scenarios(): void
    {
        // Create more than 3 scenarios to test pagination
        Scenario::factory()->count(5)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('scenarios');
        
        $scenarios = $response->viewData('scenarios');
        $this->assertEquals(3, $scenarios->count()); // Custom pagination
    }
} 