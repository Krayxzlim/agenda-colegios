@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>Colegios</h2>
    <button class="btn btn-primary">Agregar Colegio</button>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($colegios as $c)
        <tr>
            <td>{{ $c->nombre }}</td>
            <td>{{ $c->direccion }}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editColegio({{ $c }})">Editar</button>
                <form action="{{ route('colegios.destroy', $c->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="colegioModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="colegioForm" method="POST" action="{{ route('colegios.store') }}">
      @csrf
      <input type="hidden" name="id" id="colegioId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Colegio</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" id="colegioNombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Dirección</label>
                <input type="text" name="direccion" id="colegioDireccion" class="form-control">
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
function editColegio(c){
    document.getElementById('colegioId').value = c.id;
    document.getElementById('colegioNombre').value = c.nombre;
    document.getElementById('colegioDireccion').value = c.direccion;
    document.querySelector('#colegioModal .modal-title').innerText = 'Editar Colegio';
    new bootstrap.Modal(document.getElementById('colegioModal')).show();
}
</script>
@endpush
