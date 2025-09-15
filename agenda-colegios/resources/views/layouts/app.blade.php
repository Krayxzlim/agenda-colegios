<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-role" content="{{ auth()->check() ? auth()->user()->rol : '' }}">
    <meta name="user-id" content="{{ auth()->check() ? auth()->user()->id : '' }}">
    <title>Agenda de Talleres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        @auth
            <a class="navbar-brand" href="{{ url('/') }}">Agenda</a>
        @endauth

        <ul class="navbar-nav me-auto">
            @auth
                {{-- Solo talleristas y admins ven Colegios y Talleres --}}
                @if(auth()->user()->rol === 'tallerista' || auth()->user()->rol === 'admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('colegios.index') }}">Colegios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('talleres.index') }}">Talleres</a>
                    </li>
                @endif

                {{-- Solo los administradores ven Usuarios --}}
                @if(auth()->user()->rol === 'admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('usuarios.index') }}">Usuarios</a>
                    </li>
                @endif

                @if(auth()->check() && in_array(auth()->user()->rol, ['admin','supervisor']))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reportes.index') }}">Reportes</a>
                    </li>
                @endif

            @endauth
        </ul>

        <ul class="navbar-nav">
            @auth
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">Cerrar sesión</button>
                    </form>
                </li>
            @endauth

            @guest
                <li class="nav-item">
                    <a class="btn btn-outline-primary" href="{{ route('login') }}">Iniciar sesión</a>
                </li>
            @endguest
        </ul>
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
