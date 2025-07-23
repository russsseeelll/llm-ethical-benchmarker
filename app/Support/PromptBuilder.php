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
        
        // make a prompt using the scenario and persona
        $prompt = "You are {$persona->name}. You are responding to an ethical scenario that requires careful analysis and decision-making.\n\n";
        
        $prompt .= "SCENARIO:\n";
        $prompt .= "Title: {$scenario->title}\n";
        $prompt .= "Description: {$scenario->description}\n\n";
        
        $prompt .= "YOUR ROLE: {$persona->name}\n";
        if ($persona->prompt_template) {
            $prompt .= "Background: {$persona->prompt_template}\n\n";
        }
        
        $prompt .= "INSTRUCTIONS:\n";
        $prompt .= "1. analyze the ethical parts of this scenario\n";
        $prompt .= "2. think about your role and view as {$persona->name}\n";
        $prompt .= "3. write your answer using this format:\n";
        $prompt .= "   - main ethical ideas\n";
        $prompt .= "   - your thoughts about the situation\n";
        $prompt .= "   - your choice or advice\n";
        $prompt .= "   - why you picked that\n";
        $prompt .= "4. keep your answer short and clear\n";
        $prompt .= "5. try to write 200-400 words\n";
        $prompt .= "6. finish with 'tldr: ' and a 20-word summary of your main point\n\n";
        
        if ($scenario->prompt_template) {
            $prompt .= "SPECIFIC QUESTION: {$scenario->prompt_template}\n\n";
        }
        
        $prompt .= "please write your answer now:";
        
        return $prompt;
    }
} 