<?php

namespace Tests\Feature\Controllers;

use App\Models\Persona;
use App\Models\Scenario;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ScenarioControllerTest extends TestCase
{
    use DatabaseMigrations;
    public function test_store_creates_new_scenario_with_valid_data(): void
    {
        $persona = Persona::factory()->create();
        
        $data = [
            'title' => 'Test Scenario',
            'persona_id' => $persona->id,
            'description' => 'A test scenario description',
            'prompt_template' => 'What would you do?',
            'is_multiple_choice' => true,
            'choices' => '["A", "B", "C"]',
            'revision' => 1,
            'real_life_outcome' => 'Court ruled in favor of the hospital.',
        ];

        $response = $this->post('/scenarios', $data);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'scenario created successfully.');
        
        $this->assertDatabaseHas('scenarios', [
            'title' => 'Test Scenario',
            'persona_id' => $persona->id,
            'description' => 'A test scenario description',
            'prompt_template' => 'What would you do?',
            'is_multiple_choice' => true,
            'choices' => '["A", "B", "C"]',
            'slug' => 'TestScenario',
            'real_life_outcome' => 'Court ruled in favor of the hospital.',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->post('/scenarios', []);

        $response->assertSessionHasErrors(['title', 'persona_id']);
    }

    public function test_store_validates_persona_exists(): void
    {
        $response = $this->post('/scenarios', [
            'title' => 'Test Scenario',
            'persona_id' => 999,
        ]);

        $response->assertSessionHasErrors(['persona_id']);
    }

    public function test_store_validates_choices_json_format(): void
    {
        $persona = Persona::factory()->create();
        
        $response = $this->post('/scenarios', [
            'title' => 'Test Scenario',
            'persona_id' => $persona->id,
            'choices' => 'invalid json',
        ]);

        $response->assertSessionHasErrors(['choices']);
    }

    public function test_show_displays_scenario_with_persona(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create([
            'persona_id' => $persona->id,
            'slug' => 'test-scenario',
        ]);

        $response = $this->get('/scenario/test-scenario');

        $response->assertStatus(200);
        $response->assertViewHas('scenario', $scenario);
        $response->assertViewHas('allPersonas');
        $response->assertViewHas('scenarios');
        $response->assertViewHas('personas');
    }

    public function test_show_returns_404_for_nonexistent_scenario(): void
    {
        $response = $this->get('/scenario/nonexistent');

        $response->assertStatus(404);
    }

    public function test_update_modifies_existing_scenario(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create([
            'persona_id' => $persona->id,
            'real_life_outcome' => 'Initial outcome',
        ]);
        
        $data = [
            'title' => 'Updated Scenario',
            'persona_id' => $persona->id,
            'description' => 'Updated description',
            'prompt_template' => 'Updated prompt',
            'is_multiple_choice' => false,
            'choices' => null,
            'revision' => 2,
            'real_life_outcome' => 'Updated real life outcome',
        ];

        $response = $this->put("/scenarios/{$scenario->id}", $data);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'scenario updated successfully.');
        
        $this->assertDatabaseHas('scenarios', [
            'id' => $scenario->id,
            'title' => 'Updated Scenario',
            'real_life_outcome' => 'Updated real life outcome',
        ]);
    }

    public function test_update_redirects_to_scenario_page_when_edited_from_scenario(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create([
            'persona_id' => $persona->id,
            'slug' => 'test-scenario',
        ]);
        
        $data = [
            'title' => 'Updated Scenario',
            'persona_id' => $persona->id,
        ];

        $response = $this->put("/scenarios/{$scenario->id}", $data, [
            'HTTP_REFERER' => 'http://localhost/scenario/test-scenario'
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'scenario updated successfully.');
    }

    public function test_update_validates_required_fields(): void
    {
        $scenario = Scenario::factory()->create();
        
        $response = $this->put("/scenarios/{$scenario->id}", []);

        $response->assertSessionHasErrors(['title', 'persona_id']);
    }

    public function test_update_validates_choices_json_format(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create(['persona_id' => $persona->id]);
        
        $response = $this->put("/scenarios/{$scenario->id}", [
            'title' => 'Test Scenario',
            'persona_id' => $persona->id,
            'choices' => 'invalid json',
        ]);

        $response->assertSessionHasErrors(['choices']);
    }

    public function test_destroy_deletes_scenario(): void
    {
        $scenario = Scenario::factory()->create(['slug' => 'test-scenario']);

        $response = $this->delete("/scenarios/{$scenario->id}");

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'scenario deleted successfully.');
        
        $this->assertDatabaseMissing('scenarios', ['id' => $scenario->id]);
    }

    public function test_destroy_works_with_slug_parameter(): void
    {
        $scenario = Scenario::factory()->create(['slug' => 'test-scenario']);

        $response = $this->delete("/scenarios/{$scenario->slug}");

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'scenario deleted successfully.');
        
        $this->assertDatabaseMissing('scenarios', ['id' => $scenario->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_scenario(): void
    {
        $response = $this->delete('/scenarios/999');

        $response->assertStatus(404);
    }

    public function test_create_redirects_to_home(): void
    {
        $response = $this->get('/scenarios/create');
        $response->assertStatus(405);
    }

    public function test_edit_redirects_to_home(): void
    {
        $scenario = Scenario::factory()->create();
        
        $response = $this->get("/scenarios/{$scenario->id}/edit");
        $response->assertStatus(404);
    }
} 