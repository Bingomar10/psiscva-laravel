@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h2>Administración de Roles</h2>
        <button id="btn_nuevo_rol" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Rol
        </button>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header navbar-custom" >
            <h3 class="card-title fw-bold m-0">Listado de Roles</h3>
        </div>
        
        <div class="card-body" data-bs-theme="light">
            <table id="tbl_roles" class="table table-bordered table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>Acciones</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal para Crear Rol -->
    <div class="modal fade" id="md_rol" tabindex="-1" aria-hidden="true" data-bs-theme="light">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form_rol">
                    <input type="hidden" id="idrol" name="idrol"> 
                    <div class="modal-header navbar-custom">
                        <h5 class="modal-title" id="md_title">Nuevo Rol</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Rol</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                    <!-- SECCIÓN DE PERMISOS -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Permisos del Sistema</label>
                            <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                                <div class="row">
                                    <!-- Checkboxes de permisos -->
                                    <div id="lista_permisos" class="col-12">
                                        <p class="text-muted">Cargando permisos...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    @vite('resources/js/pages/roles.js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop