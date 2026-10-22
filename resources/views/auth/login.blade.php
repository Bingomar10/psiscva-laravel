@extends('layouts.auth')

@section('content')
<div class="contenedor">
    
    <!-- Encabezado PERAZIN -->
    <hr style="border-top: 2px solid white; margin: 1px 1% 1px 1%; width: 98%;">
    <div class="card-header" style="text-align: center;">
        <h2 style="color: #f9b757; margin-bottom: 0px;">P E R A Z I N</h2>
        <h6 style="color: #78cfd9; margin-top: 0px;">TECNOLOGÍA Y ACCESORIOS</h6>
    </div>
    <hr style="border-top: 2px solid white; margin: 1px 1% 1px 1%; width: 98%;">
    
    <h2 style="color: aqua; margin-top: 30px;">Inicio de sesión</h2>

    <!-- Formulario apuntando a la ruta de login de Laravel -->
    <form method="POST" action="{{ route('login') }}" id="frmAcceso" class="button-container">
        @csrf

        <!-- Campo Usuario -->
        <div class="usuario">
            <input type="text" id="nombre_usuario" name="nombre_usuario" class="form-control" value="{{ old('nombre_usuario') }}" required autofocus autocomplete="username">
            <span class="fas fa-user-alt fa-xs form-control-feedback" style="color: aqua;"></span>
            <label>Usuario</label>
        </div>
        @error('nombre_usuario')
            <span style="color: #ff6b6b; font-size: 13px; display: block; margin-top: 5px; text-align: left;">{{ $message }}</span>
        @enderror

        <!-- Campo Contraseña -->
        <div class="usuario">
            <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
            <span class="fas fa-eye fa-xs form-control-feedback" id="togglePassword" onclick="verContra()" style="color: aqua; cursor: pointer;"></span>
            <label>Contraseña</label>
        </div>
        @error('password')
            <span style="color: #ff6b6b; font-size: 13px; display: block; margin-top: 5px; text-align: left;">{{ $message }}</span>
        @enderror
        
        <!-- Recuperación de contraseña -->
        <div style="margin-top: 15px; text-align: left;">
            @if (Route::has('password.request'))
                <a class="olvidocontra" href="{{ route('password.request') }}">¿Has olvidado tu Contraseña?</a>
            @endif
        </div>

        <!-- Botón Animado -->
        <div>
            <button type="submit" style="color: white;" id="btnIngresar">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <b>INGRESAR</b>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Función para ver/ocultar la contraseña
    function verContra() {
        var input = document.getElementById("password");
        var icon = document.getElementById("togglePassword");
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

@endpush