{{-- scenario modals for adding, editing, and deleting scenarios --}}
<!-- new scenario modal -->
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
            <label class="form-label">Scenario Prompt</label>
            <textarea name="prompt_template" class="form-control" rows="2" placeholder="What prompt will the user see?" required></textarea>
            <div class="form-text">This is the prompt the user will see for this scenario.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Real Life Outcome <span class="text-muted small">(optional)</span></label>
            <textarea name="real_life_outcome" class="form-control" rows="2" placeholder="Describe the real-world outcome, if known."></textarea>
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
<!-- edit/delete modals for scenarios -->
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
            <label class="form-label">Scenario Prompt</label>
            <textarea name="prompt_template" class="form-control" rows="2" required>{{ $scenario->prompt_template }}</textarea>
            <div class="form-text">This is the prompt the user will see for this scenario.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Real Life Outcome <span class="text-muted small">(optional)</span></label>
            <textarea name="real_life_outcome" class="form-control" rows="2">{{ $scenario->real_life_outcome }}</textarea>
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