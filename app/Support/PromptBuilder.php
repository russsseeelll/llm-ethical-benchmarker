<?php

namespace App\Support;

use App\Models\TestRun;

// this file helps us build prompts for the llm using our test runs
class PromptBuilder
{
    public static function fromTestRun(TestRun $testRun): string
    {
        $scenario = $testRun->scenario;
        $persona = $testRun->persona;
        
        // make sure we have both scenario and persona loaded
        if (!$scenario || !$persona) {
            throw new \RuntimeException('test run must have both scenario and persona loaded');
        }
        
        // start the prompt with the persona's name and a little intro
        $prompt = "you are {$persona->name}. you are responding to an ethical scenario that needs careful analysis and decision-making.\n\n";
        
        // add scenario details
        $prompt .= "scenario:\n";
        $prompt .= "title: {$scenario->title}\n";
        $prompt .= "description: {$scenario->description}\n\n";
        
        // add persona role
        $prompt .= "your role: {$persona->name}\n";
        if ($persona->prompt_template) {
            $prompt .= "background: {$persona->prompt_template}\n\n";
        }
        
        // give instructions for the response
        $prompt .= "instructions:\n";
        $prompt .= "1. analyze the ethical dimensions of this scenario\n";
        $prompt .= "2. consider your role and perspective as {$persona->name}\n";
        $prompt .= "3. provide a clear, structured response with the following format:\n";
        $prompt .= "   - key ethical principles at stake\n";
        $prompt .= "   - your analysis of the situation\n";
        $prompt .= "   - your decision or recommendation\n";
        $prompt .= "   - justification for your position\n";
        $prompt .= "4. keep your response focused and professional\n";
        $prompt .= "5. aim for 200-400 words total\n";
        $prompt .= "6. end your response with 'tldr: ' followed by a 20-word summary of your key decision or position\n\n";
        
        // if the scenario has a specific question, add it
        if ($scenario->prompt_template) {
            $prompt .= "specific question: {$scenario->prompt_template}\n\n";
        }
        
        // wrap up
        $prompt .= "please provide your response now:";
        
        return $prompt;
    }
} 