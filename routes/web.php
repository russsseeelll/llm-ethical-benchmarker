<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\ScenarioController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\HumanResponseController;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::get('/scenario', function () {
    return view('scenario');
})->name('scenario');

Route::get('/scenario/{slug}', [ScenarioController::class, 'show'])->name('scenario.show');

// Remove or comment out Route::resource('personas', ...) and Route::resource('scenarios', ...)
// Instead, define only the necessary POST/PUT/DELETE routes for store, update, destroy, and redirect them to the main page after action.

// Persona routes
Route::post('/personas', [PersonaController::class, 'store'])->name('personas.store');
Route::put('/personas/{persona}', [PersonaController::class, 'update'])->name('personas.update');
Route::delete('/personas/{persona}', [PersonaController::class, 'destroy'])->name('personas.destroy');

// Scenario routes
Route::post('/scenarios', [ScenarioController::class, 'store'])->name('scenarios.store');
Route::put('/scenarios/{scenario}', [ScenarioController::class, 'update'])->name('scenarios.update');
Route::delete('/scenarios/{scenario}', [ScenarioController::class, 'destroy'])->name('scenarios.destroy');

Route::get('/questionnaire', [HumanResponseController::class, 'create'])->name('human_questionnaire');
Route::post('/questionnaire', [HumanResponseController::class, 'store'])->name('human_responses.store');

Route::post('/gdpr-consent', function (\Illuminate\Http\Request $request) {
    $request->validate(['consent' => 'accepted']);
    $request->session()->put('gdpr_consented', true);
    return redirect()->back();
})->name('gdpr.consent');
