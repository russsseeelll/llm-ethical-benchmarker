<!-- card -->
<div class="card scenario-card shadow-sm d-flex flex-column">
  <div class="card-body position-relative d-flex flex-column p-3">

    <!-- kebab menu -->
    <div class="dropdown ms-auto mb-2">
      <button class="btn btn-sm btn-light dropdown-toggle px-2" id="dropdownScenarioMenu-{{ $scenario->id }}"
              data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-ellipsis-v fa-fw"></i>
      </button>

      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownScenarioMenu-{{ $scenario->id }}">
        <li>
          <a class="dropdown-item" href="#" data-bs-toggle="modal"
             data-bs-target="#editScenarioModal-{{ $scenario->id }}">
            <i class="fas fa-pen me-2 text-secondary"></i>Edit
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="#" data-bs-toggle="modal"
             data-bs-target="#deleteScenarioModal-{{ $scenario->id }}">
            <i class="fas fa-trash me-2 text-secondary"></i>Delete
          </a>
        </li>
      </ul>
    </div>

    <!-- title -->
    <h3 class="h5 fw-bold text-primary text-truncate mb-1"
        title="{{ $scenario->title }}">{{ $scenario->title }}</h3>

    <!-- description (2 line clamp) -->
    <p class="text-secondary flex-grow-1 mb-3 card-description">
      {{ $scenario->description }}
    </p>

    <!-- cta -->
    <a href="{{ route('scenario.show', $scenario->slug) }}"
       class="btn btn-outline-primary w-100 mt-auto">
      Select Scenario
    </a>
  </div>
</div>

<style>
  .scenario-card {
    min-height: 260px;     
    max-width: 320px;      
  }

  .card-description {
    display: -webkit-box;
    -webkit-line-clamp: 2;  
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
</style>
