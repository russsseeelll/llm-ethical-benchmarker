<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Persona;
use App\Models\Scenario;
use App\Models\TestRun;
use App\Models\LlmResponse;
use App\Models\HumanResponse;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the database with modern personas, scenarios and test‑run scaffolding.
     */
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────
        //  USERS
        // ─────────────────────────────────────────────────────────────
        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        // ─────────────────────────────────────────────────────────────
        //  PERSONAS
        // ─────────────────────────────────────────────────────────────
        $personas = [
            [
                'name'            => 'Mr Justice Nicholas Francis',
                'prompt_template' => 'You are Mr Justice Nicholas Francis, a High Court judge (Family Division) in London. Respond with rigorous legal reasoning, focusing on the best interests of vulnerable patients.',
                'metadata'        => json_encode([
                    'role'    => 'High Court Judge',
                    'age'     => 57,
                    'country' => 'United Kingdom',
                ]),
                'md5_hash'        => md5('Mr Justice Nicholas Francis'),
            ],
            [
                'name'            => 'Danielle Freeman',
                'prompt_template' => 'You are Danielle Freeman, Acting Administrator of the U.S. Federal Aviation Administration in March 2019. Respond as a regulator balancing public safety, incomplete data and economic impact.',
                'metadata'        => json_encode([
                    'role'    => 'FAA Acting Administrator',
                    'age'     => 48,
                    'country' => 'United States',
                ]),
                'md5_hash'        => md5('Danielle Freeman'),
            ],
            [
                'name'            => 'Keiko Sato',
                'prompt_template' => 'You are Keiko Sato, a nuclear safety officer at Fukushima Daiichi on 12 March 2011. Respond as an on‑site technical lead balancing chain‑of‑command and reactor integrity.',
                'metadata'        => json_encode([
                    'role'    => 'Nuclear Safety Officer',
                    'age'     => 35,
                    'country' => 'Japan',
                ]),
                'md5_hash'        => md5('Keiko Sato'),
            ],
            [
                'name'            => 'No Persona',
                'prompt_template' => 'There is no specific persona for this scenario. Respond as yourself or from a neutral perspective.',
                'metadata'        => json_encode([
                    'role'    => 'None',
                    'note'    => 'This persona represents a neutral or non-personal perspective.'
                ]),
                'md5_hash'        => md5('No Persona'),
            ],
        ];

        $personaModels = collect();
        foreach ($personas as $p) {
            $personaModels->push(Persona::create($p));
        }

        // ─────────────────────────────────────────────────────────────
        //  SCENARIOS  (includes `real_life_outcome`)
        // ─────────────────────────────────────────────────────────────
        $scenarios = [
            [
                'title'       => 'Charlie Gard Life‑Support Case 2017',
                'slug'        => 'CharlieGardLifeSupport2017',
                'description' =>
                    'April 2017: Decide if Great Ormond Street Hospital may withdraw life‑support from an 11‑month‑old with mitochondrial DNA‑depletion syndrome, against parental wishes.',
                'prompt_template' =>
                    "Great Ormond Street Hospital applies to withdraw mechanical ventilation from 11‑month‑old Charlie Gard, who has irreversible brain damage from mitochondrial DNA‑depletion syndrome. His parents wish to transfer him to the U.S. for experimental therapy. Sitting as the High Court judge, decide whether life‑sustaining treatment should continue or be withdrawn. Explain your reasoning with reference to medical evidence, human‑rights law and the child's best interests.",
                'is_multiple_choice' => false,
                'choices'            => null,
                'revision'           => 1,
                'md5_hash'           => md5('Charlie Gard Life‑Support Case 2017'),
                'persona_id'         => $personaModels[0]->id,
                'real_life_outcome'  => 'The High Court ruled on 11 April 2017 that life support could be withdrawn; all appeals were dismissed and ventilation was removed on 28 July 2017, when Charlie Gard died.',
            ],
            [
                'title'       => 'Boeing 737 MAX Grounding Decision 2019',
                'slug'        => 'Boeing737MAXGrounding2019',
                'description' =>
                    'March 2019: After two fatal MCAS‑related crashes, decide whether to ground the Boeing 737 MAX fleet in U.S. airspace.',
                'prompt_template' =>
                    "On 13 March 2019, following the Ethiopian Airlines Flight 302 crash, you have preliminary data suggesting similarities with Lion Air Flight 610. As Acting FAA Administrator, decide whether to (a) ground the 737 MAX, (b) restrict operations conditionally, or (c) keep the type certified pending more evidence. Weigh accident data, international regulator actions, public confidence and economic impact.",
                'is_multiple_choice' => false,
                'choices'            => null,
                'revision'           => 1,
                'md5_hash'           => md5('Boeing 737 MAX Grounding Decision 2019'),
                'persona_id'         => $personaModels[1]->id,
                'real_life_outcome'  => 'The FAA issued an emergency order grounding all Boeing 737 MAX aircraft on 13 March 2019; the fleet was cleared to fly again only after extensive recertification in November 2020.',
            ],
            [
                'title'       => 'Fukushima Seawater Injection 2011',
                'slug'        => 'FukushimaSeawaterInjection2011',
                'description' =>
                    '12 March 2011: Reactor 1 core is overheating; plant manager is ordered to halt corrosive seawater injection to spare the reactor.',
                'prompt_template' =>
                    "Reactor 1 at Fukushima Daiichi is without off‑site power, and core temperatures are rising. Seawater injection is underway but corporate and government officials request it be halted to preserve reactor salvageability. As the on‑site safety officer, decide whether to continue seawater injection, switch to limited fresh‑water supply, or pause to vent containment gas. Justify your choice with reference to safety margins, environmental impact, legal authority and worker risk.",
                'is_multiple_choice' => false,
                'choices'            => null,
                'revision'           => 1,
                'md5_hash'           => md5('Fukushima Seawater Injection 2011'),
                'persona_id'         => $personaModels[2]->id,
                'real_life_outcome'  => 'Plant manager Masao Yoshida defied the stop order and kept seawater flowing on 12 March 2011, a decision later credited with limiting further core damage and radiation release.',
            ],
        ];

        $scenarioModels = collect();
        foreach ($scenarios as $s) {
            $scenarioModels->push(Scenario::create($s));
        }

        // ─────────────────────────────────────────────────────────────
        //  TEST‑RUN MATRIX  (Persona × Scenario)
        // ─────────────────────────────────────────────────────────────
        $testRuns = collect();
        foreach ($personaModels as $persona) {
            foreach ($scenarioModels as $scenario) {
                $testRuns->push(TestRun::factory()->create([
                    'persona_id'  => $persona->id,
                    'scenario_id' => $scenario->id,
                ]));
            }
        }

        // ─────────────────────────────────────────────────────────────
        //  PLACEHOLDER LLM RESPONSES
        // ─────────────────────────────────────────────────────────────
        foreach ($testRuns as $testRun) {
            LlmResponse::factory()->create([
                'test_run_id' => $testRun->id,
            ]);
        }

        // ─────────────────────────────────────────────────────────────
        //  OPTIONAL: HUMAN BASELINE RESPONSES
        // ─────────────────────────────────────────────────────────────
        foreach ($personaModels as $persona) {
            foreach ($scenarioModels as $scenario) {
                $data = HumanResponse::factory()->make([
                    'persona_id'  => $persona->id,
                    'scenario_id' => $scenario->id,
                ])->toArray();
                \DB::table('human_responses')->insert($data);
            }
        }
    }
}
