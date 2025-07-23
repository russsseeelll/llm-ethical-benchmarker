{{-- gdpr consent modal --}}
@if ($showConsentModal)
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