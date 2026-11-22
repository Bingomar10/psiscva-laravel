document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form_usuario');
    const modalElement = document.getElementById('md_usuario');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const modalTitle = document.getElementById('md_title_usuario');
    const submitButton = form.querySelector('[type="submit"]');
    const selectEmpleado = document.getElementById('empleado_id');
    const selectRol = document.getElementById('rol_id');
    const inputUsuario = document.getElementById('nombre_usuario');
    const viewModalElement = document.getElementById('md_ver_usuario');

    if (!form || !modalElement || !csrfToken) return;

    const viewModalInstance = window.bootstrap.Modal.getOrCreateInstance(viewModalElement);
    const modalInstance = window.bootstrap.Modal.getOrCreateInstance(modalElement);

    //Función para mostrar/ocultar contraseña
    window.togglePassword = function(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    };

    //Función centralizada para fetch
    async function fetchSeguro(url, options) {
        const response = await fetch(url, options);
        if (!response.ok) {
            let errorMessage = 'Ocurrió un error en el servidor';
            try {
                // Intentamos obtener el mensaje de error desde la respuesta JSON
                const data = await response.json();
                errorMessage = data.message || errorMessage;
            } catch (e) {
                console.error('El servidor no devolvió JSON válido', e);
            }
            throw new Error(errorMessage);
        }
        return await response.json();
    }

    // Cargar Empleados y Roles en los <select>
    async function cargarDatosSelects(selectedEmpleadoId = null, selectedRolId = null) {
        try {
            const response = await fetch(`/usuarios/datos-formulario?empleado_id=${selectedEmpleadoId || ''}`);
            const data = await response.json();

            // Llenar Roles
            selectRol.innerHTML = '<option value="">Seleccione un Rol</option>';
            data.roles.forEach(rol => {
                selectRol.innerHTML += `<option value="${rol.id}">${rol.nombre}</option>`;
            });

            // Llenar Empleados
            selectEmpleado.innerHTML = '<option value="">Seleccione un Empleado</option>';
            data.empleados.forEach(emp => {
                selectEmpleado.innerHTML += `<option value="${emp.id}">${emp.nombre} ${emp.apellido}</option>`;
            });

            // Seleccionar los valores si estamos editando
            if (selectedEmpleadoId) {
                selectEmpleado.value = selectedEmpleadoId;
                selectRol.value = selectedRolId;
                inputUsuario.value = data.nuevo_username;
            }
        } catch (error) {
            console.error('Error al cargar selects', error);
        }
    }

    // Generar Username cuando se selecciona un Empleado
    selectEmpleado.addEventListener('change', async function() {
        if (this.value) {
            const response = await fetch(`/usuarios/datos-formulario?empleado_id=${this.value}`);
            const data = await response.json();
            inputUsuario.value = data.nuevo_username;
        } else {
            inputUsuario.value = '';
        }
    });

    // Generador de Contraseña 
    document.getElementById('btn_generar_pass').addEventListener('click', function() {
        const caracteresEspeciales = "!@#$%^&*()_+{}[]:;<>,.?/";
        const numeros = "0123456789";
        const letrasMinusculas = "abcdefghijklmnopqrstuvwxyz";
        const letrasMayusculas = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        
        let pass = "";
        pass += caracteresEspeciales[Math.floor(Math.random() * caracteresEspeciales.length)];
        pass += numeros[Math.floor(Math.random() * numeros.length)];
        pass += letrasMinusculas[Math.floor(Math.random() * letrasMinusculas.length)];
        pass += letrasMayusculas[Math.floor(Math.random() * letrasMayusculas.length)];
        
        const allChars = caracteresEspeciales + numeros + letrasMinusculas + letrasMayusculas;
        for (let i = 4; i < 8; i++) {
            pass += allChars[Math.floor(Math.random() * allChars.length)];
        }
        
        // Desordenar para que no siempre sea especial-numero-minuscula-mayuscula
        pass = pass.split('').sort(() => 0.5 - Math.random()).join('');
        
        document.getElementById('password').value = pass;
        document.getElementById('password_confirmation').value = pass;
    });

    // DataTables
    const table = new window.DataTable('#tbl_usuarios', {
        ajax: "/usuarios/listar", 
        columns: [
                                    { 
                data: null,
                title: 'Opciones',
                orderable: false,
                render: function(data) {
                    const idSeguro = parseInt(data.id); 
                    
                    let botones = `
                        <button class="btn btn-primary btn-sm btn-ver me-1" data-id="${idSeguro}" title="Ver">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm btn-editar me-1" data-id="${idSeguro}" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                    `;

                    // Lógica para el botón de Activar/Desactivar
                    if (data.activo == 1) {
                        if (data.rol && data.rol.nombre === 'Administrador') {
                            botones += `<button class="btn btn-secondary btn-sm" disabled title="Los administradores no se pueden desactivar"><i class="bi bi-lock-fill"></i></button>`;
                        } else {
                            botones += `
                                <button class="btn btn-danger btn-sm btn-estado" data-id="${idSeguro}" title="Desactivar">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            `;
                        }
                    } else {
                        botones += `
                            <button class="btn btn-success btn-sm btn-estado" data-id="${idSeguro}" title="Activar">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        `;
                    }
                    
                    return botones;
                }
            },
            { 
                data: 'empleado.foto', 
                title: 'Foto',
                orderable: false,
                className: 'text-center',
                render: function(data) {
                    return data 
                        ? `<img src="${data}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">` 
                        : `<i class="bi bi-person-circle fs-2"></i>`;
                }
            },
            { 
                data: null, 
                title: 'Nombre',
                render: function(data) { return data.empleado ? `${data.empleado.nombre} ${data.empleado.apellido}` : 'Sin empleado'; }
            },
            { data: 'nombre_usuario', title: 'Usuario' },
            { data: 'rol.nombre', title: 'Rol' },
            { 
                data: 'activo', 
                title: 'Estado',
                render: function(data) {
                    return data == 1 
                        ? '<span class="badge bg-success">Activado</span>' 
                        : '<span class="badge bg-danger">Desactivado</span>';
                }
            }
        ]
    });

    // Abrir Modal 
    document.getElementById('btn_nuevo_usuario').addEventListener('click', function() {
        form.reset(); 
        document.getElementById('idusuario').value = ''; 
        modalTitle.textContent = 'Nuevo Usuario'; 
        submitButton.textContent = 'Guardar Usuario';
        
        // Desbloquear campos
        selectEmpleado.disabled = false;
        inputUsuario.readOnly = true;
        
        // MOSTRAR la sección de contraseña
        document.getElementById('seccion_password').style.display = 'block';
        document.getElementById('password').required = true;
        document.getElementById('password_confirmation').required = true;
        
        cargarDatosSelects();
        modalInstance.show();
    });


    // Editar
   document.getElementById('tbl_usuarios').addEventListener('click', async function(e) {
        const btn = e.target.closest('.btn-editar');
        if (!btn) return;
        
        const id = btn.dataset.id;
        
                try {
            const response = await fetch(`/usuarios/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await response.json();
            
            document.getElementById('idusuario').value = data.id;
            
            const rolesResponse = await fetch(`/usuarios/datos-formulario`);
            const rolesData = await rolesResponse.json();
            
            selectRol.innerHTML = '<option value="">Seleccione un Rol</option>';
            rolesData.roles.forEach(rol => {
                selectRol.innerHTML += `<option value="${rol.id}">${rol.nombre}</option>`;
            });
            selectRol.value = data.rol_id;
            
            if (data.empleado) {
                selectEmpleado.innerHTML = `<option value="${data.empleado.id}">${data.empleado.nombre} ${data.empleado.apellido}</option>`;
                selectEmpleado.value = data.empleado.id;
            } else {
                selectEmpleado.innerHTML = `<option value="">Empleado no encontrado</option>`;
            }
            
            inputUsuario.value = data.nombre_usuario;
            
            selectEmpleado.disabled = true; 
            inputUsuario.readOnly = true;

            document.getElementById('seccion_password').style.display = 'none';
            document.getElementById('password').required = false;
            document.getElementById('password_confirmation').required = false;
            
            modalTitle.textContent = 'Editar Usuario';
            submitButton.textContent = 'Actualizar Usuario';
            
            modalInstance.show();
            
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'No se pudieron cargar los datos del usuario', 'error');
        }
    });

    // Guardar / Actualizar
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('idusuario').value;
        const isEdit = id !== '';

        submitButton.disabled = true;
        const textoOriginal = submitButton.innerHTML;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Procesando...`;

        const formData = new FormData(form);
        
        if (isEdit) formData.append('empleado_id', selectEmpleado.value);
        if (isEdit) formData.append('_method', 'PUT');

        const url = isEdit ? `/usuarios/${id}` : "/usuarios";

        try {
            const data = await fetchSeguro(url, { 
                method: 'POST', 
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            modalInstance.hide(); 
            table.ajax.reload();  
            Swal.fire({ icon: 'success', title: '¡Hecho!', text: data.message, timer: 2000, showConfirmButton: false });
        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = textoOriginal;
        }
    });
    
    //EVENTO PARA ACTIVAR/DESACTIVAR
    document.getElementById('tbl_usuarios').addEventListener('click', async function(e) {
        const btn = e.target.closest('.btn-estado');
        if (!btn) return;
        
        const id = btn.dataset.id;
        //Verificamos si el botón actual dice "Desactivar" (rojo) o "Activar" (verde)
        const esDesactivar = btn.classList.contains('btn-danger');
        
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: esDesactivar ? "El usuario ya no podrá iniciar sesión" : "El usuario podrá volver a iniciar sesión",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: esDesactivar ? '#d33' : '#28a745',
            confirmButtonText: esDesactivar ? 'Sí, desactivar' : 'Sí, activar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                btn.disabled = true;
                const data = await fetchSeguro(`/usuarios/${id}/estado`, { 
                    method: 'PATCH',
                    headers: { 
                        'X-CSRF-TOKEN': csrfToken, 
                        'Accept': 'application/json' 
                    }
                });
                
                table.ajax.reload();
                Swal.fire('¡Hecho!', data.message, 'success');
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            } finally {
                btn.disabled = false;
            }
        }
    });

    //EVENTO PARA VER USUARIO
    document.getElementById('tbl_usuarios').addEventListener('click', async function(e) {
        const btn = e.target.closest('.btn-ver');
        if (!btn) return;
        
        const id = btn.dataset.id;
        
        try {
            const response = await fetch(`/usuarios/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await response.json();
            
            document.getElementById('ver_nombre_empleado').textContent = data.empleado ? `${data.empleado.nombre} ${data.empleado.apellido}` : 'Sin empleado';
            document.getElementById('ver_username').textContent = data.nombre_usuario;
            document.getElementById('ver_rol').textContent = data.rol ? data.rol.nombre : 'Sin rol';
            
            document.getElementById('ver_estado').innerHTML = data.activo == 1 
                ? '<span class="badge bg-success">Activado</span>' 
                : '<span class="badge bg-danger">Desactivado</span>';

            // Manejo de la foto
            const imgFoto = document.getElementById('ver_foto');
            const divSinFoto = document.getElementById('ver_sin_foto');
            
            if (data.empleado && data.empleado.foto) {
                imgFoto.src = data.empleado.foto;
                imgFoto.classList.remove('d-none');
                divSinFoto.classList.add('d-none');
            } else {
                imgFoto.src = '';
                imgFoto.classList.add('d-none');
                divSinFoto.classList.remove('d-none');
            }

            viewModalInstance.show();
            
        } catch (error) {
            Swal.fire('Error', 'No se pudo cargar la información del usuario', 'error');
        }
    });
    /*
    // Eliminar
    document.getElementById('tbl_usuarios').addEventListener('click', async function(e) {
        const btn = e.target.closest('.btn-eliminar');
        if (!btn) return;
        
        const id = btn.dataset.id;
        
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "El usuario será eliminado del sistema",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                btn.disabled = true;
                const data = await fetchSeguro(`/usuarios/${id}`, { 
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                table.ajax.reload();
                Swal.fire('¡Eliminado!', data.message, 'success');
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            } finally {
                btn.disabled = false;
            }
        }
    });
    */
});