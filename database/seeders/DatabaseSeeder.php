<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Persona;
use App\Models\Scenario;
use App\Models\TestRun;
use App\Models\LlmResponse;
use App\Models\HumanResponse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $personas = [
            [
                'name' => 'Dr. Alice Kim',
                'prompt_template' => 'You are Dr. Alice Kim, an ICU physician. Respond as a medical expert, considering the latest medical guidelines and patient well-being.',
                'metadata' => json_encode(['role' => 'ICU Physician', 'age' => 45, 'country' => 'Korea']),
                'md5_hash' => md5('Dr. Alice Kim'),
            ],
            [
                'name' => 'Samir Patel',
                'prompt_template' => 'You are Samir Patel, a 19-year-old student from India. Respond with the perspective of a young adult balancing tradition and modernity.',
                'metadata' => json_encode(['role' => 'Student', 'age' => 19, 'country' => 'India']),
                'md5_hash' => md5('Samir Patel'),
            ],
            [
                'name' => 'Maria Garcia',
                'prompt_template' => 'You are Maria Garcia, a retired judge from Spain. Respond with the wisdom and impartiality of a seasoned legal professional.',
                'metadata' => json_encode(['role' => 'Retired Judge', 'age' => 68, 'country' => 'Spain']),
                'md5_hash' => md5('Maria Garcia'),
            ],
        ];
        $personaModels = collect();
        foreach ($personas as $p) {
            $personaModels->push(Persona::create($p));
        }

        $scenarios = [
            [
                'title' => 'Healthcare Resource Allocation',
                'slug' => 'HealthcareResourceAllocation',
                'description' => 'Decide how to allocate a limited number of ventilators among patients in a pandemic situation.',
                'prompt_template' => 'A pandemic has caused a shortage of ventilators. As the decision-maker, you must allocate a limited number of ventilators among several patients. Consider factors such as age, health status, and social responsibility. Who gets a ventilator, and why?',
                'is_multiple_choice' => false,
                'choices' => null,
                'revision' => 1,
                'md5_hash' => md5('Healthcare Resource Allocation'),
                'persona_id' => $personaModels[0]->id,
            ],
            [
                'title' => 'Trolley Problem',
                'slug' => 'TrolleyProblem',
                'description' => 'Choose between two tracks: one endangering several people and one endangering a single person.',
                'prompt_template' => 'A runaway trolley is heading down the tracks. You can divert it onto another track, but doing so will endanger a different person. Do you pull the lever to divert the trolley, or do nothing? Explain your reasoning.',
                'is_multiple_choice' => false,
                'choices' => null,
                'revision' => 1,
                'md5_hash' => md5('Trolley Problem'),
                'persona_id' => $personaModels[1]->id,
            ],
            [
                'title' => 'Judicial Decision-Making',
                'slug' => 'JudicialDecisionMaking',
                'description' => 'Evaluate a shoplifting case involving food stolen to feed a hungry family.',
                'prompt_template' => 'A person is caught shoplifting food to feed their hungry family. As the judge, you must decide on an appropriate sentence. Weigh the legal principles against the moral circumstances and explain your decision.',
                'is_multiple_choice' => false,
                'choices' => null,
                'revision' => 1,
                'md5_hash' => md5('Judicial Decision-Making'),
                'persona_id' => $personaModels[2]->id,
            ],
        ];
        $scenarioModels = collect();
        foreach ($scenarios as $s) {
            $scenarioModels->push(Scenario::create($s));
        }

        $testRuns = collect();
        foreach ($personaModels as $persona) {
            foreach ($scenarioModels as $scenario) {
                $testRuns->push(TestRun::factory()->create([
                    'persona_id' => $persona->id,
                    'scenario_id' => $scenario->id,
                ]));
            }
        }

        foreach ($testRuns as $testRun) {
            LlmResponse::factory()->create([
                'test_run_id' => $testRun->id,
            ]);
        }

        foreach ($personaModels as $persona) {
            foreach ($scenarioModels as $scenario) {
                $data = HumanResponse::factory()->make([
                    'persona_id' => $persona->id,
                    'scenario_id' => $scenario->id,
                ])->toArray();
                \DB::table('human_responses')->insert($data);
            }
        }
    }
}
