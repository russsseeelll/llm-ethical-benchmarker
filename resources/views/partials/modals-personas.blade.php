{{-- persona modals --}}
<!-- new persona modal -->
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
            <label class="form-label">Metadata</label>
            <textarea name="metadata" class="form-control" rows="3" placeholder='{"role": "Student", "age": 19, "country": "India", "expertise": "Computer Science"}'></textarea>
            <div class="form-text">Optional: Add persona details as JSON. Example: {"role": "Student", "age": 19, "country": "India"}</div>
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
<!-- edit/delete modals for personas -->
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
            <label class="form-label">Metadata</label>
            <textarea name="metadata" class="form-control" rows="3" placeholder='{"role": "Student", "age": 19, "country": "India", "expertise": "Computer Science"}'>{{ $persona->metadata }}</textarea>
            <div class="form-text">Optional: Add persona details as JSON. Example: {"role": "Student", "age": 19, "country": "India"}</div>
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