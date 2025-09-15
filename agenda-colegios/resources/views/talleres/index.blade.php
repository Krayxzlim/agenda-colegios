@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>Talleres</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tallerModal">Agregar Taller</button>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($talleres as $t)
        <tr>
            <td>{{ $t->nombre }}</td>
            <td>{{ $t->descripcion }}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editTaller({{ $t }})">Editar</button>
                <form action="{{ route('talleres.destroy', $t->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="tallerModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="tallerForm" method="POST" action="{{ route('talleres.store') }}">
      @csrf
      <input type="hidden" name="id" id="tallerId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Taller</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" id="tallerNombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Descripción</label>
                <textarea name="descripcion" id="tallerDescripcion" class="form-control"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function editTaller(t){
    document.getElementById('tallerId').value = t.id;
    document.getElementById('tallerNombre').value = t.nombre;
    document.getElementById('tallerDescripcion').value = t.descripcion;
    document.querySelector('#tallerModal .modal-title').innerText = 'Editar Taller';
    new bootstrap.Modal(document.getElementById('tallerModal')).show();
}
</script>
@endpush
