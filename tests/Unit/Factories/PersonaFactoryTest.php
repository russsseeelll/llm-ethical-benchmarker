<?php

namespace Tests\Unit\Factories;

use App\Models\Persona;
use App\Models\Scenario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonaFactoryTest extends TestCase
{
    use RefreshDatabase;
    public function test_persona_factory_creates_valid_persona(): void
    {
        $persona = Persona::factory()->create();

        $this->assertInstanceOf(Persona::class, $persona);
        $this->assertNotEmpty($persona->name);
        $this->assertNotNull($persona->md5_hash);
        $this->assertDatabaseHas('personas', ['id' => $persona->id]);
    }

    public function test_persona_factory_creates_with_custom_data(): void
    {
        $persona = Persona::factory()->create([
            'name' => 'Custom Persona',
            'prompt_template' => 'Custom template',
            'metadata' => '{"custom": "data"}',
        ]);

        $this->assertEquals('Custom Persona', $persona->name);
        $this->assertEquals('Custom template', $persona->prompt_template);
        $this->assertEquals('{"custom": "data"}', $persona->metadata);
    }

    public function test_persona_factory_creates_multiple_personas(): void
    {
        $personas = Persona::factory()->count(3)->create();

        $this->assertCount(3, $personas);
        $this->assertCount(3, Persona::all());
    }

    public function test_persona_factory_generates_unique_names(): void
    {
        $persona1 = Persona::factory()->create();
        $persona2 = Persona::factory()->create();

        $this->assertNotEquals($persona1->name, $persona2->name);
    }

    public function test_persona_factory_creates_with_scenarios(): void
    {
        $persona = Persona::factory()
            ->has(Scenario::factory()->count(2))
            ->create();

        $this->assertCount(2, $persona->scenarios);
        $this->assertInstanceOf(Scenario::class, $persona->scenarios->first());
    }
} 