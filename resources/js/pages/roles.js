document.addEventListener('DOMContentLoaded', () => {
    // Referencias y validación
    const form = document.getElementById('form_rol');
    const modalElement = document.getElementById('md_rol');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const modalTitle = document.getElementById('md_title');
    
    if (!form || !modalElement || !csrfToken) {
        console.error('No se encontraron los elementos necesarios del módulo de roles.');
        return;
    }

    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
    const submitButton = form.querySelector('[type="submit"]');

    // Función centralizada para fetch
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

    // Inicialización de DataTables
    const table = new DataTable('#tbl_roles', {
        ajax: "/roles/listar", 
        columns: [
            { 
                data: null,
                title: 'Acciones',
                orderable: false,
                render: function(data, type, row) {
                    const idSeguro = parseInt(row.id); 
                     return `
                        <button class="btn btn-primary btn-sm btn-editar me-1" data-id="${idSeguro}" title="Editar">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="${idSeguro}" title="Eliminar">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    `;
                }
            },
            { 
                data: 'nombre',
                title: 'Nombre',
            }       
        ]
    });

    // Eventos de interfaz
    document.getElementById('btn_nuevo_rol').addEventListener('click', function() {
        form.reset(); 
        document.getElementById('idrol').value = ''; 
        modalTitle.textContent = 'Nuevo Rol'; 
        submitButton.textContent = 'Guardar';
        
        // Limpiamos los checkboxes de permisos
        document.querySelectorAll('.chk-permiso').forEach(chk => chk.checked = false);
        modalInstance.show();
    });

    document.getElementById('tbl_roles').addEventListener('click', async function(e) {
        const btn = e.target.closest('.btn-editar');
        if (!btn) return;
        
        const id = btn.dataset.id;
        
        try {
            const response = await fetch(`/roles/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await response.json();
            
            // Llenamos el formulario
            document.getElementById('idrol').value = data.id;
            document.getElementById('nombre').value = data.nombre;

            // Marcamos los checkboxes de permisos según los permisos del rol
            const permisosAsignados = data.permisos.map(p => p.id);
            
            document.querySelectorAll('.chk-permiso').forEach(chk => {
                // Si el ID del checkbox está en la lista de permisosAsignados, lo marcamos
                chk.checked = permisosAsignados.includes(parseInt(chk.value));
            });
            
            modalTitle.textContent = 'Editar Rol';
            submitButton.textContent = 'Actualizar';
            
            modalInstance.show();
            
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar los datos del rol', 'error');
        }
    });

    let todosLosPermisos = []; 

    async function cargarPermisos() {
        try {
            const response = await fetch('/permisos/listar'); 
            todosLosPermisos = await response.json();
            
            const contenedor = document.getElementById('lista_permisos');
            contenedor.innerHTML = ''; // Limpiamos el "Cargando..."

            // Creamos los checkboxes
            todosLosPermisos.forEach(permiso => {
                contenedor.innerHTML += `
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input chk-permiso" type="checkbox" name="permisos[]" value="${permiso.id}" id="perm_${permiso.id}">
                        <label class="form-check-label" for="perm_${permiso.id}">
                            ${permiso.nombre}
                        </label>
                    </div>
                `;
            });
        } catch (error) {
            console.error('Error al cargar permisos', error);
        }
    }

    cargarPermisos();

    // Operaciones HTTP
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('idrol').value;
        const isEdit = id !== ''; // Si hay ID, es edición. Si no, es guardado.

        submitButton.disabled = true;
        const textoOriginal = submitButton.innerHTML;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...`;

        const formData = new FormData(form);

        // FormData no soporta PUT, así que enviamos POST con un _method=PUT
        if (isEdit) formData.append('_method', 'PUT');

        const url = isEdit ? `/roles/${id}` : "/roles";

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
            Swal.fire({ 
                icon: 'success', 
                title: '¡Hecho!', 
                text: data.message, 
                timer: 2000, 
                showConfirmButton: false 
            });
        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = textoOriginal;
        }
    });

    document.getElementById('tbl_roles').addEventListener('click', async function(e) {
        const btn = e.target.closest('.btn-eliminar');
        if (!btn) return;
        
        const id = btn.dataset.id;
        
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "El rol será eliminado del sistema",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                btn.disabled = true;

                const data = await fetchSeguro(`/roles/${id}`, { 
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': csrfToken, 
                        'Accept': 'application/json' 
                    }
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
});