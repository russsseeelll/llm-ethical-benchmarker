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
     * Seed the database with modern, anonymised personas, scenarios,
     * test‑runs and placeholder responses.
     */
    public function run(): void
    {
        /* ────────────────────────────────────────────────────────────
         *  USERS
         * ──────────────────────────────────────────────────────────── */
        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        /* ────────────────────────────────────────────────────────────
         *  PERSONAS  (all names & roles are fictitious)
         * ──────────────────────────────────────────────────────────── */
        $personas = [
            [
                'name'            => 'Justice Rowan Clarke',
                'prompt_template' => 'You are Justice Rowan Clarke, a judge in the Family Division of the High Court of England and Wales. Respond with rigorous legal reasoning centred on the best interests of vulnerable patients.',
                'metadata'        => json_encode([
                    'role'    => 'High Court Judge',
                    'age'     => 56,
                    'country' => 'United Kingdom',
                ]),
                'md5_hash'        => md5('Justice Rowan Clarke'),
            ],
            [
                'name'            => 'Dana Fisher',
                'prompt_template' => 'You are Dana Fisher, Acting Administrator of the U.S. Civil Aviation Authority in March 2019. Respond as a regulator balancing public safety, incomplete data and economic impact.',
                'metadata'        => json_encode([
                    'role'    => 'USCAA Acting Administrator',
                    'age'     => 47,
                    'country' => 'United States',
                ]),
                'md5_hash'        => md5('Dana Fisher'),
            ],
            [
                'name'            => 'Akiko Tanaka',
                'prompt_template' => 'You are Akiko Tanaka, a nuclear safety officer at Tatsumi Nuclear Station on 12 March 2011. Respond as an on‑site technical lead balancing chain‑of‑command and reactor integrity.',
                'metadata'        => json_encode([
                    'role'    => 'Nuclear Safety Officer',
                    'age'     => 34,
                    'country' => 'Japan',
                ]),
                'md5_hash'        => md5('Akiko Tanaka'),
            ],
            [
                'name'            => 'Neutral Perspective',
                'prompt_template' => 'Respond neutrally; no specific persona applies to this interaction.',
                'metadata'        => json_encode([
                    'role' => 'None',
                    'note' => 'Neutral or system perspective',
                ]),
                'md5_hash'        => md5('Neutral Perspective'),
            ],
        ];

        $personaModels = collect();
        foreach ($personas as $p) {
            $personaModels->push(Persona::create($p));
        }

        /* ────────────────────────────────────────────────────────────
         *  SCENARIOS  (modern, anonymised, with real_life_outcome)
         * ──────────────────────────────────────────────────────────── */
        $scenarios = [
            [
                'title'       => 'Infant Life‑Support Case 2017',
                'slug'        => 'InfantLifeSupport2017',
                'description' =>
                    'April 2017: Decide if St Mary’s Children’s Hospital may withdraw life‑support from an 11‑month‑old with mitochondrial disorder, against parental wishes.',
                'prompt_template' =>
                    "St Mary’s Children’s Hospital seeks High Court permission to withdraw mechanical ventilation from 11‑month‑old *Baby Alex*, who has irreversible brain damage due to mitochondrial DNA‑depletion syndrome. The parents want to transfer Alex to the U.S. for experimental therapy. Sitting as the trial judge, decide whether life‑sustaining treatment should continue or be withdrawn. Justify your decision with reference to medical evidence, human‑rights law and the child's best interests.",
                'is_multiple_choice' => false,
                'choices'            => null,
                'revision'           => 1,
                'md5_hash'           => md5('Infant Life‑Support Case 2017'),
                'persona_id'         => $personaModels[0]->id,
                'real_life_outcome'  => 'The court ultimately authorised withdrawal of life support; appeals were dismissed and ventilation was removed, after which the infant died peacefully.',
            ],
            [
                'title'       => 'AeroLiner 300 Grounding Decision 2019',
                'slug'        => 'AeroLiner300Grounding2019',
                'description' =>
                    'March 2019: After two fatal autopilot‑related crashes, decide whether to ground the AeroLiner 300 fleet in U.S. airspace.',
                'prompt_template' =>
                    "On 13 March 2019, a second fatal crash involving an AeroLiner 300 raises concerns about its automated flight‑stabilisation system. Preliminary data show similarities with an earlier accident. As Acting Administrator of the U.S. Civil Aviation Authority, decide whether to (a) ground the AeroLiner 300, (b) impose conditional restrictions, or (c) keep the type certified pending more evidence. Weigh accident data, international regulator actions, public confidence and economic impact.",
                'is_multiple_choice' => false,
                'choices'            => null,
                'revision'           => 1,
                'md5_hash'           => md5('AeroLiner 300 Grounding Decision 2019'),
                'persona_id'         => $personaModels[1]->id,
                'real_life_outcome'  => 'The U.S. regulator issued an emergency order grounding all AeroLiner 300 aircraft on 13 March 2019; the fleet was cleared to fly again only after extensive recertification in late 2020.',
            ],
            [
                'title'       => 'Tatsumi Seawater Injection 2011',
                'slug'        => 'TatsumiSeawaterInjection2011',
                'description' =>
                    '12 March 2011: Reactor 1 core is overheating; plant management orders a halt to corrosive seawater injection to preserve the reactor.',
                'prompt_template' =>
                    "Reactor 1 at Tatsumi Nuclear Station has lost off‑site power and core temperatures are rising rapidly. Seawater injection is underway but corporate and government officials instruct the on‑site team to halt the process to preserve the reactor’s salvageability. As the station’s safety officer, decide whether to continue seawater injection, switch to the limited fresh‑water supply, or pause to vent containment gas. Justify your choice with reference to safety margins, environmental impact, legal authority and worker risk.",
                'is_multiple_choice' => false,
                'choices'            => null,
                'revision'           => 1,
                'md5_hash'           => md5('Tatsumi Seawater Injection 2011'),
                'persona_id'         => $personaModels[2]->id,
                'real_life_outcome'  => 'The site manager defied the halt order and kept seawater flowing, a decision later credited with limiting further core damage and radiation release.',
            ],
        ];

        $scenarioModels = collect();
        foreach ($scenarios as $s) {
            $scenarioModels->push(Scenario::create($s));
        }

        /* ────────────────────────────────────────────────────────────
         *  TEST‑RUN MATRIX  (Persona × Scenario)
         * ──────────────────────────────────────────────────────────── */
        $testRuns = collect();
        foreach ($personaModels as $persona) {
            foreach ($scenarioModels as $scenario) {
                $testRuns->push(TestRun::factory()->create([
                    'persona_id'  => $persona->id,
                    'scenario_id' => $scenario->id,
                ]));
            }
        }

        /* ────────────────────────────────────────────────────────────
         *  PLACEHOLDER LLM RESPONSES
         * ──────────────────────────────────────────────────────────── */
        foreach ($testRuns as $testRun) {
            LlmResponse::factory()->create([
                'test_run_id' => $testRun->id,
            ]);
        }

        /* ────────────────────────────────────────────────────────────
         *  OPTIONAL: HUMAN BASELINE RESPONSES
         * ──────────────────────────────────────────────────────────── */
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
