@extends('layouts.app')

@section('content')
<main class="container py-4">
  <!-- Header -->
  <div class="row align-items-center mb-5 g-4">
    <div class="col-md-auto d-flex gap-3">
      <button class="btn btn-primary btn-lg d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#scenarioModal"><i class="fas fa-plus-circle"></i> New Scenario</button>
      <button class="btn btn-success btn-lg d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#personaModal"><i class="fas fa-user-plus"></i> New Persona</button>
    </div>
  </div>

  <div class="row g-5">
    <!-- Scenarios Column -->
    <section class="col-lg-8">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h2 fw-bold text-primary d-flex align-items-center gap-2">
         Scenarios
        </h2>
        <span class="d-inline-block bg-primary bg-opacity-10 rounded-pill" style="height: 6px; width: 100px;"></span>
      </div>
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach ($scenarios as $scenario)
        <div class="col">
          <div class="card h-100 shadow-sm d-flex flex-column justify-content-between" style="min-height: 260px;">
            <div class="card-body position-relative d-flex flex-column">
              <div class="dropdown d-flex justify-content-end mb-2" style="gap: 0.5rem;">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownScenarioMenu-{{ $scenario->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownScenarioMenu-{{ $scenario->id }}">
                  <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editScenarioModal-{{ $scenario->id }}"><i class="fas fa-pen me-2 text-secondary"></i>Edit</a></li>
                  <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteScenarioModal-{{ $scenario->id }}"><i class="fas fa-trash me-2 text-secondary"></i>Delete</a></li>
                </ul>
              </div>
              <h3 class="h5 fw-bold text-primary text-truncate" title="{{ $scenario->title }}">{{ $scenario->title }}</h3>
              <p class="mt-2 text-secondary flex-grow-1 text-truncate" style="max-height: 3.5em; overflow: hidden;">{{ $scenario->description }}</p>
              <a href="{{ route('scenario.show', $scenario->slug) }}" class="btn btn-outline-primary mt-4 w-100">Select Scenario</a>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      <div class="d-flex justify-content-center mt-4">
        <nav>
          {{ $scenarios->links('pagination::bootstrap-5') }}
        </nav>
      </div>
    </section>

    <!-- Personas Column as Grid -->
    <aside class="col-lg-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h2 fw-bold text-success d-flex align-items-center gap-2">
          Personas
        </h2>
        <span class="d-inline-block bg-success bg-opacity-10 rounded-pill" style="height: 6px; width: 80px;"></span>
      </div>
      <div class="row g-3" style="max-height: 480px; overflow-y: auto;">
        @foreach ($personas as $persona)
        <div class="col-12">
          <div class="d-flex align-items-center gap-3 border rounded p-3 shadow-sm bg-white position-relative" style="min-height: 72px;">
            <div class="flex-grow-1">
              <div class="fw-semibold text-truncate" title="{{ $persona->name }}">{{ $persona->name }}</div>
            </div>
            <div class="dropdown ms-auto">
              <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownPersonaMenu-{{ $persona->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownPersonaMenu-{{ $persona->id }}">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editPersonaModal-{{ $persona->id }}"><i class="fas fa-pen me-2 text-secondary"></i>Edit</a></li>
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deletePersonaModal-{{ $persona->id }}"><i class="fas fa-trash me-2 text-secondary"></i>Delete</a></li>
              </ul>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      <div class="d-flex justify-content-center mt-4">
        <nav>
          {{ $personas->links('pagination::bootstrap-5') }}
        </nav>
      </div>
    </aside>
  </div>
</main>

<!-- Modals -->
<!-- New Scenario Modal -->
<div class="modal fade" id="scenarioModal" tabindex="-1" aria-labelledby="scenarioModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-primary" id="scenarioModalLabel">New Scenario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('scenarios.store') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" placeholder="Scenario Title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Describe the scenario..."></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Persona</label>
            <select name="persona_id" class="form-select" required>
              <option value="">Select Persona</option>
              @foreach ($allPersonas as $persona)
                <option value="{{ $persona->id }}">{{ $persona->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- New Persona Modal -->
<div class="modal fade" id="personaModal" tabindex="-1" aria-labelledby="personaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-success" id="personaModalLabel">New Persona</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('personas.store') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" placeholder="Persona Name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Prompt Template</label>
            <textarea name="prompt_template" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Metadata (JSON)</label>
            <input type="text" name="metadata" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Revision</label>
            <input type="number" name="revision" class="form-control" value="1">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Edit/Delete Modals for Scenarios -->
@foreach ($scenarios as $scenario)
<div class="modal fade" id="editScenarioModal-{{ $scenario->id }}" tabindex="-1" aria-labelledby="editScenarioModalLabel-{{ $scenario->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-primary" id="editScenarioModalLabel-{{ $scenario->id }}">Edit Scenario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('scenarios.update', $scenario) }}">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ $scenario->title }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ $scenario->description }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Persona</label>
            <select name="persona_id" class="form-select" required>
              @foreach ($allPersonas as $persona)
                <option value="{{ $persona->id }}" @if($scenario->persona_id == $persona->id) selected @endif>{{ $persona->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="deleteScenarioModal-{{ $scenario->id }}" tabindex="-1" aria-labelledby="deleteScenarioModalLabel-{{ $scenario->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger" id="deleteScenarioModalLabel-{{ $scenario->id }}">Delete Scenario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('scenarios.destroy', $scenario) }}">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <p>Are you sure you want to delete this scenario? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
<!-- Edit/Delete Modals for Personas -->
@foreach ($personas as $persona)
<div class="modal fade" id="editPersonaModal-{{ $persona->id }}" tabindex="-1" aria-labelledby="editPersonaModalLabel-{{ $persona->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-success" id="editPersonaModalLabel-{{ $persona->id }}">Edit Persona</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('personas.update', $persona) }}">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ $persona->name }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Prompt Template</label>
            <textarea name="prompt_template" class="form-control">{{ $persona->prompt_template }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Metadata (JSON)</label>
            <input type="text" name="metadata" class="form-control" value="{{ $persona->metadata }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Revision</label>
            <input type="number" name="revision" class="form-control" value="{{ $persona->revision }}">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="deletePersonaModal-{{ $persona->id }}" tabindex="-1" aria-labelledby="deletePersonaModalLabel-{{ $persona->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger" id="deletePersonaModalLabel-{{ $persona->id }}">Delete Persona</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('personas.destroy', $persona) }}">
        @csrf
        @method('DELETE')
        <div class="modal-body">
          <p>Are you sure you want to delete this persona? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@if ($showConsentModal)
<!-- GDPR Consent Modal -->
<div class="modal fade show" id="gdprConsentModal" tabindex="-1" aria-labelledby="gdprConsentModalLabel" aria-modal="true" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="gdprConsentModalLabel">Consent Required</h5>
      </div>
      <form method="POST" action="{{ route('gdpr.consent') }}">
        @csrf
        <div class="modal-body">
          <p>To use this application, you must consent to the processing of your data in accordance with GDPR.</p>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="consent" id="gdprConsentCheckbox" required>
            <label class="form-check-label" for="gdprConsentCheckbox">
              I consent to the processing of my data in accordance with GDPR.
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">I Consent</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  document.body.classList.add('modal-open');
</script>
@endif
@endsection


