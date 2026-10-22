<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inicio de Sesión</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Bootstrap 4.6.1 -->
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}">
    <!-- SweetAlert 2 -->
    <link rel="stylesheet" href="{{ asset('sweetalert2/sweetalert2.min.css') }}">
    <!-- Mis colores -->
    <link rel="stylesheet" href="{{ asset('css/css-nuevo/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/css-nuevo/iconos.css') }}">
</head>
<body>

    @yield('content')
    <!-- Bootstrap 4.6.1 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- SweetAlert 2 -->
    <script src="{{ asset('sweetalert2/sweetalert2.all.min.js') }}"></script>

    @stack('scripts')
</body>
</html>

