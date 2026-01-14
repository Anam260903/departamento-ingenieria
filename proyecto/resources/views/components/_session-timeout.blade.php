@if (Auth::check())
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endif

{{-- Alerta de inactividad para cierre de sesión --}}
<div class="modal fade" id="sessionWarningModal" tabindex="-1" aria-labelledby="sessionWarningLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sessionWarningLabel">⚠️ Alerta de Inactividad</h5>
      </div>
      <div class="modal-body">
        Tu sesión está a punto de expirar debido a la inactividad. Serás desconectado en 
        <span id="countdownDisplay" class="fw-bold text-danger">60</span> segundos.
        <br><br>
        ¿Deseas permanecer conectado?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary w-auto" onclick="window.location.href='/login'"> Cerrar Sesión </button>
        <button type="button" class="btn btn-primary w-auto" id="extendSessionButton">Permanecer conectado</button>
      </div>
    </div>
  </div>
</div>
@if (Auth::check())
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('js/session-timeout.js') }}"></script>
@endif