<?php

namespace Tests\Feature\Controllers;

use App\Models\Persona;
use App\Models\Scenario;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PersonaControllerTest extends TestCase
{
    use DatabaseMigrations;
    public function test_store_creates_new_persona_with_valid_data(): void
    {
        $data = [
            'name' => 'Test Persona',
            'prompt_template' => 'You are a helpful assistant.',
            'metadata' => '{"key": "value"}',
            'revision' => 1,
        ];

        $response = $this->post('/personas', $data);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Persona created successfully.');
        
        $this->assertDatabaseHas('personas', [
            'name' => 'Test Persona',
            'prompt_template' => 'You are a helpful assistant.',
            'metadata' => '{"key": "value"}',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->post('/personas', []);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_store_validates_name_max_length(): void
    {
        $response = $this->post('/personas', [
            'name' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_store_validates_metadata_json_format(): void
    {
        $response = $this->post('/personas', [
            'name' => 'Test Persona',
            'metadata' => 'invalid json',
        ]);

        $response->assertSessionHasErrors(['metadata']);
    }

    public function test_update_modifies_existing_persona(): void
    {
        $persona = Persona::factory()->create();
        
        $data = [
            'name' => 'Updated Persona',
            'prompt_template' => 'Updated template',
            'metadata' => '{"updated": true}',
            'revision' => 2,
        ];

        $response = $this->put("/personas/{$persona->id}", $data);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Persona updated successfully.');
        
        $this->assertDatabaseHas('personas', [
            'id' => $persona->id,
            'name' => 'Updated Persona',
            'prompt_template' => 'Updated template',
            'metadata' => '{"updated": true}',
        ]);
    }

    public function test_update_validates_required_fields(): void
    {
        $persona = Persona::factory()->create();
        
        $response = $this->put("/personas/{$persona->id}", []);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_update_validates_metadata_json_format(): void
    {
        $persona = Persona::factory()->create();
        
        $response = $this->put("/personas/{$persona->id}", [
            'name' => 'Test Persona',
            'metadata' => 'invalid json',
        ]);

        $response->assertSessionHasErrors(['metadata']);
    }

    public function test_destroy_deletes_persona_without_scenarios(): void
    {
        $persona = Persona::factory()->create();

        $response = $this->delete("/personas/{$persona->id}");

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Persona deleted successfully.');
        
        $this->assertDatabaseMissing('personas', ['id' => $persona->id]);
    }

    public function test_destroy_fails_when_persona_has_scenarios(): void
    {
        $persona = Persona::factory()->create();
        Scenario::factory()->create(['persona_id' => $persona->id]);

        $response = $this->delete("/personas/{$persona->id}");

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
        
        $this->assertDatabaseHas('personas', ['id' => $persona->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_persona(): void
    {
        $response = $this->delete('/personas/999');

        $response->assertStatus(404);
    }

    public function test_create_redirects_to_home(): void
    {
        $response = $this->get('/personas/create');

        $response->assertRedirect('/');
    }

    public function test_show_redirects_to_home(): void
    {
        $persona = Persona::factory()->create();
        
        $response = $this->get("/personas/{$persona->id}");

        $response->assertRedirect('/');
    }

    public function test_edit_redirects_to_home(): void
    {
        $persona = Persona::factory()->create();
        
        $response = $this->get("/personas/{$persona->id}/edit");

        $response->assertRedirect('/');
    }
} 