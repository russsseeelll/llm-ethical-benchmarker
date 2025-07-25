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
        // make a persona with the factory
        $persona = Persona::factory()->create();

        // check that it's a persona and has the stuff we want
        $this->assertInstanceOf(Persona::class, $persona);
        $this->assertNotEmpty($persona->name);
        $this->assertNotNull($persona->md5_hash);
        $this->assertDatabaseHas('personas', ['id' => $persona->id]);
    }

    public function test_persona_factory_creates_with_custom_data(): void
    {
        // make a persona with custom data
        $persona = Persona::factory()->create([
            'name' => 'custom persona',
            'prompt_template' => 'custom template',
            'metadata' => '{"custom": "data"}',
        ]);

        // check that the data is what we set
        $this->assertEquals('custom persona', $persona->name);
        $this->assertEquals('custom template', $persona->prompt_template);
        $this->assertEquals('{"custom": "data"}', $persona->metadata);
    }

    public function test_persona_factory_creates_multiple_personas(): void
    {
        // make a few personas
        $personas = Persona::factory()->count(3)->create();

        // check that we got 3
        $this->assertCount(3, $personas);
        $this->assertCount(3, Persona::all());
    }

    public function test_persona_factory_generates_unique_names(): void
    {
        // make two personas and check their names are different
        $persona1 = Persona::factory()->create();
        $persona2 = Persona::factory()->create();

        $this->assertNotEquals($persona1->name, $persona2->name);
    }

    public function test_persona_factory_creates_with_scenarios(): void
    {
        // make a persona with two scenarios
        $persona = Persona::factory()
            ->has(Scenario::factory()->count(2))
            ->create();

        // check that the persona has two scenarios
        $this->assertCount(2, $persona->scenarios);
        $this->assertInstanceOf(Scenario::class, $persona->scenarios->first());
    }
} 