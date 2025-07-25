<!-- this is our persona card component, shows a persona in a nice box -->
<div class="d-flex align-items-center gap-3 border rounded p-3 shadow-sm bg-white position-relative" style="min-height: 72px;">
  <div class="flex-grow-1">
    <div class="fw-semibold text-truncate" title="{{ $persona->name }}">{{ $persona->name }}</div>
    @if(!empty($persona->prompt_template))
      <!-- show the prompt template if we have one -->
      <div class="small text-success mt-1"><strong>Prompt Template:</strong> {{ $persona->prompt_template }}</div>
    @endif
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