<!-- this is our generic modal component, us use it for all kinds of popups -->
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-{{ $color ?? 'primary' }}" id="{{ $id }}Label">{{ $title }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      {{ $slot }}
    </div>
  </div>
</div> 