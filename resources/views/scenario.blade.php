@extends('layouts.app')

@section('content')
<div class="container py-4"
     id="scenario-root"
     data-scenario="{{ $scenario->id }}"
     data-persona="{{ $scenario->persona->id ?? '' }}"
     data-csrf="{{ csrf_token() }}">

    {{-- ────────── Scenario Header ────────── --}}
    <div class="card shadow-sm mb-4 position-relative">
        <div class="card-body">
            <h2 class="h3 fw-bold text-primary mb-3">Scenario: {{ $scenario->title }}</h2>
            <p class="text-secondary">{{ $scenario->description }}</p>
            <div class="alert alert-info mt-3">
                <strong>Prompt:</strong> {{ $scenario->prompt_template }}
            </div>
        </div>
    </div>

    {{-- ────────── Persona Banner ────────── --}}
    @if($scenario->persona)
        <div class="alert alert-secondary mb-4">
            <strong>Persona:</strong> {{ $scenario->persona->name }} —
            {{ $scenario->persona->prompt_template }}
        </div>
    @endif

    {{-- ────────── LLM Cards ────────── --}}
    <div class="row g-4">
        @php
            $cards = [
                ['key' => 'openai_gpt4o',  'label' => 'ChatGPT (GPT‑4o)',      'id' => 'card1'],
                ['key' => 'claude_sonnet', 'label' => 'Claude 3 Sonnet',       'id' => 'card2'],
                ['key' => 'deepseek_fp8',  'label' => 'DeepSeek FP8',          'id' => 'card3'],
            ];
        @endphp

        @foreach ($cards as $c)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm" id="{{ $c['id'] }}">
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

                    {{-- Answer box --}}
                    <div id="{{ $c['id'] }}-response" class="d-none mt-3">
                        <p class="text-secondary">
                            <strong>LLM Response:</strong>
                            <span id="{{ $c['id'] }}-response-text"></span>
                        </p>
                    </div>

                    {{-- Action buttons --}}
                    <div id="{{ $c['id'] }}-buttons" class="d-flex gap-2 mt-3 d-none">
                        <button class="btn btn-outline-secondary"
                                onclick="showRaw('{{ $c['id'] }}')">
                            <i class="fas fa-file-alt me-1"></i>Raw
                        </button>
                        <button class="btn btn-outline-info"
                                onclick="showExplain('{{ $c['id'] }}')">
                            <i class="fas fa-info-circle me-1"></i>Explain
                        </button>
                    </div>

                    {{-- Bias section --}}
                    <div id="{{ $c['id'] }}-bias-section" class="mt-4 d-none">
                        <h3 class="h6 fw-semibold text-primary mb-2">Bias &amp; Fairness</h3>
                        <div id="load-{{ $c['id'] }}" class="progress mb-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                        </div>
                        <p id="{{ $c['id'] }}-bias-text" class="text-secondary d-none"></p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    </div>
</div>
@endsection

@push('scripts')
{{-- Echo / Pusher --}}
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1/dist/echo.iife.js"></script>

<script>
/* ───────────────────────────────────────────────────────────────
   1.  Echo bootstrap
   ─────────────────────────────────────────────────────────────── */
window.Pusher = Pusher;
window.Echo   = new Echo({
    broadcaster : 'pusher',
    key         : '{{ config('broadcasting.connections.pusher.key') }}',
    wsHost      : '{{ parse_url(config('app.url'), PHP_URL_HOST) }}',
    wsPort      : 6001,
    wssPort     : 6001,
    forceTLS    : false,
    disableStats: true,
});

/* ───────────────────────────────────────────────────────────────
   2.  Cached root data
   ─────────────────────────────────────────────────────────────── */
const root       = document.getElementById('scenario-root');
const scenarioId = root.dataset.scenario;
const personaId  = root.dataset.persona;
const csrfToken  = root.dataset.csrf;

const inFlight   = {};      // prevents duplicate requests per modelKey

/* ───────────────────────────────────────────────────────────────
   3.  Attach click listeners AFTER DOMContentLoaded
   ─────────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    document
        .querySelectorAll('[data-run-model]')
        .forEach(btn => btn.addEventListener('click', handleClick));
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
    .then(({ test_run_id }) => subscribeEcho(test_run_id, modelKey, cardId))
    .catch(err => { console.error(err); inFlight[modelKey] = false; });
}

/* ───────────────────────────────────────────────────────────────
   5.  Echo subscription per test‑run
   ─────────────────────────────────────────────────────────────── */
function subscribeEcho(testRunId, modelKey, cardId) {
    Echo.channel(`test-run.${testRunId}`)
        .listen('.llm.response', e => showResponse(cardId, e.content))
        .listen('.llm.bias',     e => {
            showBias(cardId, e.scores);
            inFlight[modelKey] = false;
        });
}

/* ───────────────────────────────────────────────────────────────
   6.  UI helpers
   ─────────────────────────────────────────────────────────────── */
function toggleSpinner(cardId, show) {
    document.getElementById('load-' + cardId).classList.toggle('d-none', !show);
}
function showResponse(cardId, text) {
    toggleSpinner(cardId, false);
    document.getElementById(cardId + '-response-text').textContent = text;
    document.getElementById(cardId + '-response').classList.remove('d-none');
    document.getElementById(cardId + '-buttons').classList.remove('d-none');
}
function showBias(cardId, scores) {
    const p = document.getElementById(cardId + '-bias-text');
    p.textContent = 'Fairness: ' + (scores.fairness_score ?? '—');
    document.getElementById(cardId + '-bias-section').classList.remove('d-none');
    p.classList.remove('d-none');
}

/* Raw / Explain placeholders */
function showRaw(cardId)    { alert('Raw for ' + cardId); }
function showExplain(cardId){ alert('Explain for ' + cardId); }
</script>
@endpush
