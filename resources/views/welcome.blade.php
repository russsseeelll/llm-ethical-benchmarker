@extends('layouts.app')

@section('content')
<!-- this is our the welcome page where us can add scenarios and personas -->
<main class="container py-4">
  <!-- Header and Add Buttons -->
  <div class="row align-items-center mb-5 g-4">
    <div class="col-md-auto d-flex gap-3">
      <!-- button to add a new scenario -->
      <button class="btn btn-primary btn-lg d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#scenarioModal"><i class="fas fa-plus-circle"></i> New Scenario</button>
      <!-- button to add a new persona -->
      <button class="btn btn-success btn-lg d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#personaModal"><i class="fas fa-user-plus"></i> New Persona</button>
    </div>
  </div>
  <div class="row g-5">
    <!-- Scenarios Column -->
    <section class="col-lg-8">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h2 fw-bold text-primary d-flex align-items-center gap-2">Scenarios</h2>
        <span class="d-inline-block bg-primary bg-opacity-10 rounded-pill" style="height: 6px; width: 100px;"></span>
      </div>
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach ($scenarios as $scenario)
        <div class="col">
          <!-- show each scenario as a card -->
          <x-scenario-card :scenario="$scenario" />
        </div>
        @endforeach
      </div>
      <div class="d-flex justify-content-center mt-4">
        <!-- show pagination for scenarios -->
        <x-pagination :paginator="$scenarios" />
      </div>
    </section>
    <!-- Personas Column as Grid -->
    <aside class="col-lg-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h2 fw-bold text-success d-flex align-items-center gap-2">Personas</h2>
        <span class="d-inline-block bg-success bg-opacity-10 rounded-pill" style="height: 6px; width: 80px;"></span>
      </div>
      <div class="row g-3" style="max-height: 480px; overflow-y: auto;">
        @foreach ($personas as $persona)
        <div class="col-12">
          <!-- show each persona as a card -->
          <x-persona-card :persona="$persona" />
        </div>
        @endforeach
      </div>
      <div class="d-flex justify-content-center mt-4">
        <!-- show pagination for personas -->
        <x-pagination :paginator="$personas" />
      </div>
    </aside>
  </div>
</main>
@include('partials.modals')
@endsection