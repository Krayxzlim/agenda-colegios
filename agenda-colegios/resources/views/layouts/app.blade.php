<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Talleres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('agenda.index') }}">Agenda</a>
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="{{ route('usuarios.index') }}">Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('colegios.index') }}">Colegios</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('talleres.index') }}">Talleres</a></li>
        </ul>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-outline-danger">Cerrar sesión</button>
        </form>
    </div>
</nav>

<div class="container">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="{{ asset('js/calendar.js') }}"></script>
@stack('scripts')
</body>
</html>
