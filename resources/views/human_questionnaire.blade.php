@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">Human Questionnaire</h1>
    <form method="POST" action="{{ route('human_responses.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Scenario</label>
            <select name="scenario_id" class="form-select" required>
                <option value="">Select Scenario</option>
                @foreach ($scenarios as $scenario)
                    <option value="{{ $scenario->id }}">{{ $scenario->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Your Response</label>
            <textarea name="response" class="form-control" rows="4" required></textarea>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="consent" id="consent" required>
            <label class="form-check-label" for="consent">
                I consent to the processing of my data in accordance with GDPR.
            </label>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection 