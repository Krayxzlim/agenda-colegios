@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div id="calendar"></div>
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
            <label>Colectivo / Colegio</label>
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
            <select multiple name="talleristas[]" id="talleristas" class="form-control">
              @foreach($usuarios as $u)
                <option value="{{ $u->id }}">{{ $u->nombre }} {{ $u->apellido }}</option>
              @endforeach
            </select>
            <small class="text-muted">Máx. 2 talleristas por evento</small>
          </div>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById("calendar");
    let modalEl = document.getElementById("eventModal");
    let form = document.getElementById("eventForm");
    let deleteBtn = document.getElementById("deleteBtn");
    let errorBox = document.getElementById("formErrors");

    // Reset modal
    function resetModal() {
        document.getElementById("modalTitle").innerText = "Agregar Evento";
        form.reset();
        document.getElementById("eventId").value = "";
        errorBox.classList.add("d-none");
        deleteBtn.style.display = "none";
    }

    // Calendar init
    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        editable: true,
        selectable: true,
        events: "/agenda/events", // carga AJAX
        dateClick: function(info) {
            resetModal();
            document.getElementById("fecha").value = info.dateStr;
            new bootstrap.Modal(modalEl).show();
        },
        eventClick: function(info) {
            resetModal();
            document.getElementById("modalTitle").innerText = "Editar Evento";
            document.getElementById("eventId").value = info.event.id;

            let start = info.event.start;
            document.getElementById("fecha").value = start.toISOString().split("T")[0];
            document.getElementById("hora").value = start.toTimeString().slice(0, 5);
            document.getElementById("colegio_id").value = info.event.extendedProps.colegio_id;
            document.getElementById("taller_id").value = info.event.extendedProps.taller_id;

            let talleristasSelect = document.getElementById("talleristas");
            Array.from(talleristasSelect.options).forEach(
                (o) => (o.selected = info.event.extendedProps.talleristas.includes(parseInt(o.value)))
            );

            deleteBtn.style.display = "inline-block";
            deleteBtn.onclick = function () {
                if (confirm("¿Seguro que deseas eliminar este evento?")) {
                    fetch(`/agenda/${info.event.id}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success){
                            info.event.remove();
                            bootstrap.Modal.getInstance(modalEl).hide();
                        } else {
                            alert("Error al eliminar");
                        }
                    });
                }
            };

            new bootstrap.Modal(modalEl).show();
        },
    });
    calendar.render();

    // Guardar evento (AJAX)
    form.onsubmit = function(e) {
        e.preventDefault();

        let talleristasSelect = document.getElementById("talleristas");
        if (talleristasSelect.selectedOptions.length > 2) {
            errorBox.textContent = "Solo puedes seleccionar hasta 2 talleristas.";
            errorBox.classList.remove("d-none");
            return;
        }

        let formData = new FormData(form);

        fetch("/agenda/store", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                calendar.refetchEvents(); // refresca sin recargar
                bootstrap.Modal.getInstance(modalEl).hide();
            } else {
                errorBox.textContent = "Error al guardar el evento.";
                errorBox.classList.remove("d-none");
            }
        })
        .catch(() => {
            errorBox.textContent = "Error de conexión.";
            errorBox.classList.remove("d-none");
        });
    };
});
</script>
@endpush
