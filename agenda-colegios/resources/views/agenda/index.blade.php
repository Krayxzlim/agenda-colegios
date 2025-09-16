@extends('layouts.app')

@section('content')
@auth
<div class="row">
    <div class="col-md-12">
        <div id="calendar"></div>
    </div>
</div>

<!-- Modal de Detalle del Evento -->
<div class="modal fade" id="eventDetailModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalTitle">Detalle del Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Colegio:</strong> <span id="detailColegio"></span></p>
        <p><strong>Taller:</strong> <span id="detailTaller"></span></p>
        <p><strong>Fecha:</strong> <span id="detailFecha"></span></p>
        <p><strong>Hora:</strong> <span id="detailHora"></span></p>
        <div id="detailTalleristasContainer">
          <p><strong>Tallerista 1:</strong> <span id="detailT1"></span></p>
          <p><strong>Tallerista 2:</strong> <span id="detailT2"></span></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="openEditModalBtn">Editar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Agregar/Editar Evento -->
<div class="modal fade" id="eventModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="eventForm">
      @csrf
      <input type="hidden" name="id" id="eventId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Agregar Evento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="formErrors" class="alert alert-danger d-none"></div>

          <div class="mb-3">
            <label>Colegio</label>
            <select name="colegio_id" id="colegio_id" class="form-control">
              @foreach($colegios as $colegio)
                <option value="{{ $colegio->id }}">{{ $colegio->nombre }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label>Taller</label>
            <select name="taller_id" id="taller_id" class="form-control">
              @foreach($talleres as $t)
                <option value="{{ $t->id }}">{{ $t->nombre }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label>Fecha</label>
            <input type="date" name="fecha" id="fecha" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Hora</label>
            <input type="time" name="hora" id="hora" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Talleristas</label>

            @if(in_array(auth()->user()->rol, ['admin','supervisor']))
                <div id="talleristaSelectContainer">
                    <select name="talleristas[]" id="tallerista1" class="form-control mb-2">
                    <option value="">-- Seleccionar Tallerista 1 --</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }}</option>
                    @endforeach
                    </select>

                    <select name="talleristas[]" id="tallerista2" class="form-control mb-2">
                    <option value="">-- Seleccionar Tallerista 2 --</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }}</option>
                    @endforeach
                    </select>
                </div>

                <div id="talleristaResumen" class="d-none">
                    <p><strong>Tallerista 1:</strong> <span id="t1Nombre"></span></p>
                    <p><strong>Tallerista 2:</strong> <span id="t2Nombre"></span></p>
                    <button type="button" class="btn btn-warning" id="editarTalleristasBtn">Remover / Editar Talleristas</button>
                </div>

                <small class="text-muted">Máx. 2 talleristas por evento</small>
            @endif

            @if(auth()->user()->rol === 'tallerista')
                <p class="text-muted">La asignación de talleristas solo puede realizarla un Admin o Supervisor.</p>
            @endif
          </div>


          <!-- Botónsolo si Admin o Supervisor -->
          @if(in_array(auth()->user()->rol, ['admin','supervisor']))
            <div class="mb-3">
              <button type="button" class="btn btn-warning" id="removeTalleristaBtn" style="display:none;">
                Remover Tallerista
              </button>
            </div>
          @endif

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="saveBtn">Guardar</button>
          <button type="button" class="btn btn-danger" id="deleteBtn">Eliminar</button>
        </div>
      </div>
    </form>
  </div>
</div>
@else
  <div class="alert alert-warning">
      Debes iniciar sesión para acceder a la agenda.
  </div>
@endauth
@endsection

@push('scripts')
<script src="{{ asset('js/calendar.js') }}"></script>
@endpush

