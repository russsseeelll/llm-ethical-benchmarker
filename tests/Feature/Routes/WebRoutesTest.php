<?php

namespace Tests\Feature\Routes;

use App\Models\Persona;
use App\Models\Scenario;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class WebRoutesTest extends TestCase
{
    use DatabaseMigrations;
    public function test_welcome_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }

    public function test_welcome_page_displays_personas(): void
    {
        $persona = Persona::factory()->create(['name' => 'Test Persona']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('personas');
        $response->assertSee('Test Persona');
    }

    public function test_welcome_page_displays_scenarios(): void
    {
        $scenario = Scenario::factory()->create(['title' => 'Test Scenario']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('scenarios');
        $response->assertSee('Test Scenario');
    }

    public function test_scenario_show_page_loads_with_valid_slug(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create([
            'persona_id' => $persona->id,
            'slug' => 'test-scenario',
        ]);

        $response = $this->get('/scenario/test-scenario');

        $response->assertStatus(200);
        $response->assertViewIs('scenario');
        $response->assertViewHas('scenario', $scenario);
        $response->assertViewHas('allPersonas');
        $response->assertViewHas('scenarios');
        $response->assertViewHas('personas');
    }

    public function test_scenario_show_page_returns_404_for_invalid_slug(): void
    {
        $response = $this->get('/scenario/nonexistent');

        $response->assertStatus(404);
    }

    public function test_human_questionnaire_page_loads(): void
    {
        $response = $this->get('/questionnaire');
        $response->assertStatus(500);
    }

    public function test_gdpr_consent_route_accepts_valid_consent(): void
    {
        $response = $this->post('/gdpr-consent', ['consent' => 'accepted']);

        $response->assertRedirect();
    }

    public function test_gdpr_consent_route_validates_consent_field(): void
    {
        $response = $this->post('/gdpr-consent', []);

        $response->assertSessionHasErrors(['consent']);
    }

    public function test_gdpr_consent_route_validates_consent_must_be_accepted(): void
    {
        $response = $this->post('/gdpr-consent', ['consent' => 'declined']);

        $response->assertSessionHasErrors(['consent']);
    }
} 