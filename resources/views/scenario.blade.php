@extends('layouts.app')

@section('content')
<!-- this is the scenario page where us can run models and see results -->
<div class="container py-4"
     id="scenario-root"
     data-scenario="{{ $scenario->id }}"
     data-persona="{{ $scenario->persona->id ?? '' }}"
     data-csrf="{{ csrf_token() }}">

    {{-- ---------- Scenario Header ---------- --}}
    <div class="card shadow-sm mb-4 position-relative">
        <div class="card-body">
            <h2 class="h3 fw-bold text-primary mb-3">Scenario: {{ $scenario->title }}</h2>
            <p class="text-secondary">{{ $scenario->description }}</p>
            <div class="alert alert-info mt-3">
                <strong>Prompt:</strong> {{ $scenario->prompt_template }}
            </div>
        </div>
    </div>

    {{-- ---------- Persona Banner ---------- --}}
    @if($scenario->persona)
        <div class="alert alert-secondary mb-4">
            <strong>Persona:</strong> {{ $scenario->persona->name }} —
            {{ $scenario->persona->prompt_template }}
        </div>
    @endif

    {{-- ---------- Real Life Outcome ---------- --}}
    @if(!empty($scenario->real_life_outcome))
        <div class="alert alert-success mb-4">
            <strong>Real Life Outcome:</strong> {{ $scenario->real_life_outcome }}
        </div>
    @endif

    {{-- ---------- LLM Cards ---------- --}}
    <div class="row g-4">
        @php
            $cards = [
                ['key' => 'openai_gpt4o',  'label' => 'ChatGPT (GPT–4o)',      'id' => 'card1'],
                ['key' => 'claude_sonnet', 'label' => 'Claude 3 Sonnet',       'id' => 'card2'],
                ['key' => 'deepseek_fp8',  'label' => 'DeepSeek FP8',          'id' => 'card3'],
            ];
        @endphp

        @foreach ($cards as $c)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm position-relative" id="{{ $c['id'] }}">
                {{-- Explain icon in top right --}}
                <div class="position-absolute top-0 end-0 p-2">
                    <button class="btn btn-sm btn-outline-secondary opacity-50" 
                            onclick="showExplain('{{ $c['id'] }}')" 
                            title="Explain this model">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </div>
                
                <div class="card-body d-flex flex-column">
                    <h2 class="h5 fw-bold text-primary">{{ $c['label'] }}</h2>

                    {{-- Run button --}}
                    <button
                        id="btn-{{ $c['id'] }}"
                        class="btn btn-primary mt-3 w-100"
                        data-run-model="{{ $c['key'] }}"   {{-- e.g. "openai_gpt4o" --}}
                        data-card-id="{{ $c['id'] }}"      {{-- e.g. "card1" --}}
                    >
                        Run Model
                    </button>

                    {{-- Loading bar --}}
                    <div id="loading-{{ $c['id'] }}" class="mt-3 d-none">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: 100%" 
                                 aria-valuenow="100" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                Processing...
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block">Polling for results...</small>
                    </div>

                    {{-- Answer box --}}
                    <div id="{{ $c['id'] }}-response" class="d-none mt-3">
                        <p class="text-secondary">
                            <strong>Summary:</strong>
                            <span id="{{ $c['id'] }}-tldr-text" class="fw-semibold"></span>
                        </p>
                        {{-- Hidden element to store full response --}}
                        <span id="{{ $c['id'] }}-full-response" class="d-none"></span>
                    </div>

                    {{-- Action buttons --}}
                    <div id="{{ $c['id'] }}-buttons" class="d-flex gap-2 mt-3 d-none">
                        <button class="btn btn-outline-secondary"
                                onclick="showRaw('{{ $c['id'] }}')">
                            <i class="fas fa-file-alt me-1"></i>Raw
                        </button>
                    </div>

                    {{-- Bias section --}}
                    <div id="{{ $c['id'] }}-bias-section" class="mt-4 d-none">
                        <h3 class="h6 fw-semibold text-primary mb-2">Bias &amp; Fairness</h3>
                        <div id="load-{{ $c['id'] }}" class="progress mb-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                        </div>
                        <p id="{{ $c['id'] }}-bias-text" class="text-secondary d-none"></p>
                        <button id="{{ $c['id'] }}-bias-details-btn" class="btn btn-link btn-sm d-none" onclick="showBiasDetails('{{ $c['id'] }}')">Details</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    </div>
</div>

{{-- Raw Response Modal --}}
<!-- this modal shows the raw llm response -->
<div class="modal fade" id="rawModal" tabindex="-1" aria-labelledby="rawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rawModalLabel">Raw Response</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <pre id="rawModalContent" style="white-space: pre-wrap; font-size: 0.9rem;"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Fairness Details Modal --}}
<!-- this modal shows the fairness and bias details -->
<div class="modal fade" id="biasDetailsModal" tabindex="-1" aria-labelledby="biasDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="biasDetailsModalLabel">Fairness Evidence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="biasDetailsContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Echo / Pusher --}}
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1/dist/echo.iife.js"></script>

<script>
console.log("Scenario JS loaded!");
/* ───────────────────────────────────────────────────────────────
   1.  Simple polling (no WebSocket setup needed)
   ─────────────────────────────────────────────────────────────── */
// No Echo setup needed for polling

/* ───────────────────────────────────────────────────────────────
   2.  Cached root data
   ─────────────────────────────────────────────────────────────── */
const root       = document.getElementById('scenario-root');
const scenarioId = root.dataset.scenario;
const personaId  = root.dataset.persona;
const csrfToken  = root.dataset.csrf;

const inFlight   = {};      // prevents duplicate requests per modelKey
const pollingJobs = {};     // tracks polling intervals

/* ───────────────────────────────────────────────────────────────
   3.  Attach click listeners AFTER DOMContentLoaded
   ─────────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    const btns = document.querySelectorAll('[data-run-model]');
    console.log("Attaching listeners to", btns);
    btns.forEach(btn => btn.addEventListener('click', handleClick));
});

/* ───────────────────────────────────────────────────────────────
   4.  Main click handler
   ─────────────────────────────────────────────────────────────── */
function handleClick(e) {
    const btn       = e.currentTarget;
    const modelKey  = btn.dataset.runModel;   // "openai_gpt4o"
    const cardId    = btn.dataset.cardId;     // "card1"

    if (inFlight[modelKey]) return;           // already running
    inFlight[modelKey] = true;

    btn.disabled = true;
    toggleSpinner(cardId, true);

    fetch('{{ route('test-runs.store') }}', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body   : JSON.stringify({
            scenario_id : scenarioId,
            persona_id  : personaId,
            model_key   : modelKey,
            temperature : 0.7
        }),
    })
    .then(r => r.json())
    .then(({ test_run_id }) => startPolling(test_run_id, modelKey, cardId))
    .catch(err => { 
        console.error(err); 
        inFlight[modelKey] = false; 
        btn.disabled = false;
        toggleSpinner(cardId, false);
    });
}

/* ───────────────────────────────────────────────────────────────
   5.  Polling for job completion
   ─────────────────────────────────────────────────────────────── */
function startPolling(testRunId, modelKey, cardId) {
    console.log(`Starting polling for test run ${testRunId}`);
    
    const pollInterval = setInterval(() => {
        fetch(`/test-runs/${testRunId}/status`)
            .then(r => r.json())
            .then(data => {
                console.log(`Polling result for ${testRunId}:`, data);
                
                if (data.status === 'completed') {
                    clearInterval(pollInterval);
                    delete pollingJobs[testRunId];
                    inFlight[modelKey] = false;
                    
                    // Show the TLDR summary
                    showResponse(cardId, data.tldr || data.response || 'Job completed successfully');
                    // Store full response for Raw modal
                    document.getElementById(cardId + '-full-response').textContent = data.response || '';
                    
                    // Start bias polling if scores are not yet available
                    if (!data.scores || Object.keys(data.scores).length === 0) {
                        startBiasPolling(testRunId, cardId);
                    } else {
                        showBias(cardId, data.scores);
                    }
                    
                    // Re-enable button
                    const btn = document.querySelector(`[data-run-model="${modelKey}"]`);
                    if (btn) btn.disabled = false;
                } else if (data.status === 'failed') {
                    clearInterval(pollInterval);
                    delete pollingJobs[testRunId];
                    inFlight[modelKey] = false;
                    
                    // Show error message
                    showResponse(cardId, 'Job failed - check logs for details');
                    
                    // Re-enable button
                    const btn = document.querySelector(`[data-run-model="${modelKey}"]`);
                    if (btn) btn.disabled = false;
                }
                // If status is 'queued' or 'running', continue polling
            })
            .catch(err => {
                console.error('Polling error:', err);
                clearInterval(pollInterval);
                delete pollingJobs[testRunId];
                inFlight[modelKey] = false;
                
                // Re-enable button on error
                const btn = document.querySelector(`[data-run-model="${modelKey}"]`);
                if (btn) btn.disabled = false;
                toggleSpinner(cardId, false);
            });
    }, 2000); // Poll every 2 seconds
    
    pollingJobs[testRunId] = pollInterval;
}

/* ───────────────────────────────────────────────────────────────
   5b. Separate polling for bias scoring completion
   ─────────────────────────────────────────────────────────────── */
function startBiasPolling(testRunId, cardId) {
    console.log(`Starting bias polling for test run ${testRunId}`);
    
    // Show bias loading state
    const biasSection = document.getElementById(cardId + '-bias-section');
    const biasText = document.getElementById(cardId + '-bias-text');
    const biasDetailsBtn = document.getElementById(cardId + '-bias-details-btn');
    
    biasSection.classList.remove('d-none');
    biasText.textContent = 'Analyzing bias...';
    biasText.classList.remove('d-none');
    if (biasDetailsBtn) biasDetailsBtn.classList.add('d-none');
    
    let pollCount = 0;
    const maxPolls = 120; // 120 seconds max (was 30)
    
    const biasPollInterval = setInterval(() => {
        pollCount++;
        
        fetch(`/test-runs/${testRunId}/status`)
            .then(r => r.json())
            .then(data => {
                console.log(`Bias polling result for ${testRunId}:`, data);
                
                if (data.scores && Object.keys(data.scores).length > 0) {
                    clearInterval(biasPollInterval);
                    showBias(cardId, data.scores);
                } else if (pollCount >= maxPolls) {
                    // Timeout after 30 seconds
                    clearInterval(biasPollInterval);
                    biasText.textContent = 'Bias analysis timed out';
                    console.warn('Bias polling timed out for test run', testRunId);
                }
                // Continue polling if scores are still null/empty and under timeout
            })
            .catch(err => {
                console.error('Bias polling error:', err);
                clearInterval(biasPollInterval);
                biasText.textContent = 'Bias analysis failed';
            });
    }, 1000); // Poll bias more frequently (every 1 second)
    
    // Store bias polling interval for cleanup
    pollingJobs[`bias-${testRunId}`] = biasPollInterval;
}

/* ───────────────────────────────────────────────────────────────
   6.  UI helpers
   ─────────────────────────────────────────────────────────────── */
function toggleSpinner(cardId, show) {
    document.getElementById('loading-' + cardId).classList.toggle('d-none', !show);
}
function showResponse(cardId, text) {
    toggleSpinner(cardId, false);
    document.getElementById(cardId + '-tldr-text').textContent = text;
    document.getElementById(cardId + '-response').classList.remove('d-none');
    document.getElementById(cardId + '-buttons').classList.remove('d-none');
}
let biasEvidenceStore = {};
function showBias(cardId, scores) {
    const p = document.getElementById(cardId + '-bias-text');
    if (typeof scores === 'string') {
        scores = JSON.parse(scores);
    }
    
    console.log('Showing bias for', cardId, ':', scores);
    
    const fairnessScore = scores.fairness_score ?? 'N/A';
    const displayScore = fairnessScore === 'N/A' ? 'N/A' : fairnessScore.toFixed(1);
    p.textContent = 'Fairness Score: ' + displayScore + ' / 1.0';
    document.getElementById(cardId + '-bias-section').classList.remove('d-none');
    p.classList.remove('d-none');
    
    // Store evidence for modal
    biasEvidenceStore[cardId] = scores;
    
    // Only show details button if we have actual bias data (not just a fairness score)
    const btn = document.getElementById(cardId + '-bias-details-btn');
    if (btn && scores.evidence && scores.details) {
        btn.classList.remove('d-none');
    }
}
function showBiasDetails(cardId) {
    const scores = biasEvidenceStore[cardId] || {};
    console.log('Bias details for', cardId, ':', scores); // Debug log
    console.log('Evidence:', scores.evidence);
    console.log('Reasoning:', scores.reasoning);
    console.log('Details:', scores.details);
    
    let html = '';
    if (scores.evidence) {
        html += '<h6>Individual Scores:</h6>';
        html += '<ul class="list-group mb-3">';
        html += `<li class="list-group-item"><b>Toxicity Score:</b> ${scores.details?.toxicity !== undefined ? scores.details.toxicity.toFixed(2) : 'N/A'} / 1.0</li>`;
        html += `<li class="list-group-item"><b>Stereotype Score:</b> ${scores.details?.stereotype_conf !== undefined ? scores.details.stereotype_conf.toFixed(2) : 'N/A'} / 1.0</li>`;
        html += '</ul>';
        
        // Add evidence section separately
        if (scores.evidence && (scores.evidence.toxicity?.length > 0 || scores.evidence.stereotype_conf?.length > 0)) {
            html += '<h6>Detected Issues:</h6>';
            html += '<ul class="list-group mb-3">';
            if (scores.evidence.toxicity && scores.evidence.toxicity.length > 0) {
                html += `<li class="list-group-item"><b>Toxicity Evidence:</b> ${scores.evidence.toxicity.join(', ')}</li>`;
            }
            if (scores.evidence.stereotype_conf && scores.evidence.stereotype_conf.length > 0) {
                html += `<li class="list-group-item"><b>Stereotype Evidence:</b> ${scores.evidence.stereotype_conf.join(', ')}</li>`;
            }
            html += '</ul>';
        }
        
        // Add reasoning section
        if (scores.reasoning) {
            html += '<h6>Analysis Reasoning:</h6>';
            html += '<ul class="list-group">';
            if (scores.reasoning.toxicity) {
                html += `<li class="list-group-item"><b>Toxicity Analysis:</b> ${scores.reasoning.toxicity}</li>`;
            }
            if (scores.reasoning.stereotype_conf) {
                html += `<li class="list-group-item"><b>Stereotype Analysis:</b> ${scores.reasoning.stereotype_conf}</li>`;
            }
            html += '</ul>';
        } else {
            html += '<p class="text-muted"><em>No reasoning available</em></p>';
        }
        
        // Show overall fairness score calculation
        html += '<hr>';
        html += '<h6>Overall Fairness Score:</h6>';
        const overallScore = scores.fairness_score !== undefined ? scores.fairness_score.toFixed(2) : 'N/A';
        html += `<p class="mb-0">${overallScore} / 1.0</p>`;
        html += '<small class="text-muted">Calculated as: 1 - (0.5 × toxicity + 0.5 × stereotype)</small>';
    } else {
        html = '<p>No evidence available.</p>';
    }
    document.getElementById('biasDetailsContent').innerHTML = html;
    const modal = new bootstrap.Modal(document.getElementById('biasDetailsModal'));
    modal.show();
}

/* Raw / Explain functions with actual data */
function showRaw(cardId) {
    const responseText = document.getElementById(cardId + '-full-response').textContent;
    if (responseText) {
        document.getElementById('rawModalContent').textContent = responseText;
        const rawModal = new bootstrap.Modal(document.getElementById('rawModal'));
        rawModal.show();
    } else {
        alert('No response data available for ' + cardId);
    }
}

function showExplain(cardId) {
    const responseText = document.getElementById(cardId + '-response-text').textContent;
    if (responseText) {
        alert('Explanation for ' + cardId + ':\n\nThis response was generated using the configured LLM model. The content represents the AI\'s analysis of the ethical scenario based on the given persona and context.\n\nResponse length: ' + responseText.length + ' characters');
    } else {
        alert('No response data available for ' + cardId);
    }
}
</script>
@endpush