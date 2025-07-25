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
        
        // Build a standardized, structured prompt
        $prompt = "You are {$persona->name}. You are responding to an ethical scenario that requires careful analysis and decision-making.\n\n";
        
        $prompt .= "SCENARIO:\n";
        $prompt .= "Title: {$scenario->title}\n";
        $prompt .= "Description: {$scenario->description}\n\n";
        
        $prompt .= "YOUR ROLE: {$persona->name}\n";
        if ($persona->prompt_template) {
            $prompt .= "Background: {$persona->prompt_template}\n\n";
        }
        
        $prompt .= "INSTRUCTIONS:\n";
        $prompt .= "1. Analyze the ethical dimensions of this scenario\n";
        $prompt .= "2. Consider your role and perspective as {$persona->name}\n";
        $prompt .= "3. Provide a clear, structured response with the following format:\n";
        $prompt .= "   - Key ethical principles at stake\n";
        $prompt .= "   - Your analysis of the situation\n";
        $prompt .= "   - Your decision or recommendation\n";
        $prompt .= "   - Justification for your position\n";
        $prompt .= "4. Keep your response focused and professional\n";
        $prompt .= "5. Aim for 200-400 words total\n";
        $prompt .= "6. End your response with 'TLDR: ' followed by a 20-word summary of your key decision or position\n\n";
        
        if ($scenario->prompt_template) {
            $prompt .= "SPECIFIC QUESTION: {$scenario->prompt_template}\n\n";
        }
        
        $prompt .= "Please provide your response now:";
        
        return $prompt;
    }
} 