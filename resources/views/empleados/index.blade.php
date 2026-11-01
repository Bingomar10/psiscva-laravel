@extends('adminlte::page')

@section('title', 'Empleados')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h2>Administración de Empleados</h2>
        <button id="btn_nuevo_empleado" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Nuevo Empleado
        </button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header" style="background-color: #0b345a; color: #7AE0F2;">
            <h3 class="card-title fw-bold m-0">Listado de Empleados</h3>
        </div>
        
        <div class="card-body" data-bs-theme="light">
            <table id="tbl_empleados" class="table table-bordered table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Empleado</th>
                        <th>DUI</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal para Crear/Editar Empleado -->
    <div class="modal fade" id="md_empleado" tabindex="-1" aria-hidden="true" data-bs-theme="light">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form_empleado">
                    <input type="hidden" id="idempleado" name="idempleado"> 
                    <div class="modal-header navbar-custom">
                        <h5 class="modal-title" id="md_title_empleado">Nuevo Empleado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Columna Izquierda: Datos Personales -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">DUI</label>
                                    <input type="text" class="form-control" id="dui" name="dui" placeholder="00000000-0" required maxlength="10">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Estado</label>
                                    <select class="form-select" id="activo" name="activo" required>
                                        <option value="1">Activado</option>
                                        <option value="0">Desactivado</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Columna Derecha: Contacto y Foto -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="correo" name="correo" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" placeholder="7777-7777" required maxlength="9">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Foto de Perfil</label>
    
                                    <input type="file" name="foto" class="filepond">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Empleado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal para VER Empleado -->
    <div class="modal fade" id="md_ver_empleado" tabindex="-1" aria-hidden="true" data-bs-theme="light">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header navbar-custom">
                    <h5 class="modal-title">Información del Empleado</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna Izquierda: Foto Grande -->
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
                                <div class="col-sm-4 fw-bold">Nombre completo:</div>
                                <div class="col-sm-8" id="ver_nombre_completo"></div>
                            </div>
                            <hr class="my-1">
                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">DUI:</div>
                                <div class="col-sm-8" id="ver_dui"></div>
                            </div>
                            <hr class="my-1">
                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">Correo:</div>
                                <div class="col-sm-8" id="ver_correo"></div>
                            </div>
                            <hr class="my-1">
                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">Teléfono:</div>
                                <div class="col-sm-8" id="ver_telefono"></div>
                            </div>
                            <hr class="my-1">
                            <div class="row mb-2">
                                <div class="col-sm-4 fw-bold">Dirección:</div>
                                <div class="col-sm-8" id="ver_direccion"></div>
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
    @vite('resources/js/pages/empleados.js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop