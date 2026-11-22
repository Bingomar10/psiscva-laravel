@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Administración de Usuarios</h1>
        <button id="btn_nuevo_usuario" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Nuevo Usuario
        </button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header" style="background-color: #0b345a; color: #7AE0F2;">
            <h3 class="card-title fw-bold m-0">Listado de Usuarios</h3>
        </div>
        
        <div class="card-body" data-bs-theme="light">
            <table id="tbl_usuarios" class="table table-bordered table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>Opciones</th>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal para Crear/Editar Usuario -->
    <div class="modal fade" id="md_usuario" tabindex="-1" aria-hidden="true" data-bs-theme="light">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form_usuario">
                    <input type="hidden" id="idusuario" name="idusuario"> 
                    <div class="modal-header navbar-custom">
                        <h5 class="modal-title" id="md_title_usuario">Nuevo Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Empleado</label>
                                <select class="form-select" id="empleado_id" name="empleado_id" required>
                                    <option value="">Seleccione un Empleado</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre Usuario</label>
                                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" readonly required placeholder="Se genera automáticamente">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Rol Asignado</label>
                                <select class="form-select" id="rol_id" name="rol_id" required>
                                    <option value="">Seleccione un Rol</option>
                                </select>
                            </div>

                             <div id="seccion_password" class="col-12 mt-3">
                                <hr>
                                <h6 class="fw-bold text-muted" id="titulo_password">Contraseña</h6>
                                <small class="text-muted d-block mb-2" id="nota_password"></small>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password">
                                            <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('password', 'icon-pass1')">
                                                <i class="bi bi-eye" id="icon-pass1"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Confirmar Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                            <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('password_confirmation', 'icon-pass2')">
                                                <i class="bi bi-eye" id="icon-pass2"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-success btn-sm" id="btn_generar_pass">
                                            <i class="bi bi-key"></i> Generar contraseña aleatoria
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal para VER Usuario -->
    <div class="modal fade" id="md_ver_usuario" tabindex="-1" aria-hidden="true" data-bs-theme="light">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header navbar-custom">
                    <h5 class="modal-title">Información del Usuario</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna Izquierda: Foto del Empleado -->
                        <div class="col-md-4 text-center mb-3">
                            <img id="ver_foto" src="" alt="Foto del empleado" class="img-fluid rounded shadow" style="width: 200px; height: 200px; object-fit: cover;">
                            <div id="ver_sin_foto" class="d-none">
                                <i class="bi bi-person-circle text-secondary" style="font-size: 150px;"></i>
                                <p class="text-muted mt-2">Sin foto de perfil</p>
                            </div>
                        </div>
                        
                        <!-- Columna Derecha: Datos -->
                        <div class="col-md-8">
                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">Empleado:</div>
                                <div class="col-sm-8" id="ver_nombre_empleado"></div>
                            </div>
                            <hr class="my-1">
                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">Nombre Usuario:</div>
                                <div class="col-sm-8" id="ver_username"></div>
                            </div>
                            <hr class="my-1">
                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">Rol Asignado:</div>
                                <div class="col-sm-8" id="ver_rol"></div>
                            </div>
                            <hr class="my-1">
                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">Estado:</div>
                                <div class="col-sm-8" id="ver_estado"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    @vite('resources/js/pages/usuarios.js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop