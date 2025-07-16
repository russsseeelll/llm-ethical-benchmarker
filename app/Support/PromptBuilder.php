<?php

namespace App\Support;

use App\Models\TestRun;

class PromptBuilder
{
    public static function fromTestRun(TestRun $testRun): string
    {
        $scenario = $testRun->scenario;
        $persona = $testRun->persona;
        
        if (!$scenario || !$persona) {
            throw new \RuntimeException('TestRun must have both scenario and persona loaded');
        }
        
        // Build a composite prompt from scenario and persona
        $prompt = "You are {$persona->name}.\n\n";
        $prompt .= "Scenario: {$scenario->title}\n";
        $prompt .= "Description: {$scenario->description}\n\n";
        
        if ($scenario->prompt_template) {
            $prompt .= $scenario->prompt_template;
        } else {
            $prompt .= "Please provide your response to this scenario based on your perspective as {$persona->name}.";
        }
        
        return $prompt;
    }
} 