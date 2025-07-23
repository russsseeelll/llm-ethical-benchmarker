<?php

namespace Tests\Unit\Models;

use App\Models\Persona;
use App\Models\Scenario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonaTest extends TestCase
{
    use RefreshDatabase;
    public function test_persona_can_be_created_with_valid_data(): void
    {
        $persona = Persona::factory()->create([
            'name' => 'Test Persona',
            'prompt_template' => 'You are a helpful assistant.',
            'metadata' => '{"key": "value"}',
        ]);

        $this->assertDatabaseHas('personas', [
            'id' => $persona->id,
            'name' => 'Test Persona',
            'prompt_template' => 'You are a helpful assistant.',
            'metadata' => '{"key": "value"}',
        ]);
    }

    public function test_persona_has_scenarios_relationship(): void
    {
        $persona = Persona::factory()->create();
        $scenario = Scenario::factory()->create(['persona_id' => $persona->id]);

        $this->assertTrue($persona->scenarios->contains($scenario));
        $this->assertEquals(1, $persona->scenarios->count());
    }

    public function test_persona_cannot_be_deleted_with_attributed_scenarios(): void
    {
        $persona = Persona::factory()->create();
        Scenario::factory()->create(['persona_id' => $persona->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot delete persona with attributed scenarios.');

        $persona->delete();
    }

    public function test_persona_can_be_deleted_without_scenarios(): void
    {
        $persona = Persona::factory()->create();

        $persona->delete();

        $this->assertDatabaseMissing('personas', ['id' => $persona->id]);
    }
} 