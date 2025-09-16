@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <h2>Usuarios</h2>
    <button class="btn btn-primary" onclick="newUser()">Agregar Usuario</button>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($usuarios as $u)
        <tr>
            <td>{{ $u->nombre }}</td>
            <td>{{ $u->apellido }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->rol }}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editUser({{ $u }})">Editar Rol</button>
                <form action="{{ route('usuarios.destroy', $u->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="userForm" method="POST" action="{{ route('usuarios.store') }}">
      @csrf
      <input type="hidden" name="id" id="userId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div id="fieldsFull">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre" id="userNombre" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Apellido</label>
                    <input type="text" name="apellido" id="userApellido" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" id="userEmail" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" id="userPassword" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label>Rol</label>
                <select name="rol" id="userRol" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="tallerista">Tallerista</option>
                    <option value="supervisor">Supervisor</option>
                </select>
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
function newUser(){
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('fieldsFull').style.display = 'block';
    document.querySelector('#userModal .modal-title').innerText = 'Agregar Usuario';
    new bootstrap.Modal(document.getElementById('userModal')).show();
}

function editUser(u){
    document.getElementById('userId').value = u.id;
    document.getElementById('fieldsFull').style.display = 'none';
    document.getElementById('userRol').value = u.rol;
    document.querySelector('#userModal .modal-title').innerText = 'Editar Rol';
    new bootstrap.Modal(document.getElementById('userModal')).show();
}
</script>
@endpush
