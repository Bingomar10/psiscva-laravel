document.addEventListener('DOMContentLoaded', () => {
    // Referencias y validación
    const form = document.getElementById('form_empleado');
    const modalElement = document.getElementById('md_empleado');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const modalTitle = document.getElementById('md_title_empleado');
    
    if (!form || !modalElement || !csrfToken) {
        console.error('No se encontraron los elementos necesarios del módulo de empleados.');
        return;
    }

    const modalInstance = window.bootstrap.Modal.getOrCreateInstance(modalElement);
    const viewModalElement = document.getElementById('md_ver_empleado');
    const viewModalInstance = window.bootstrap.Modal.getOrCreateInstance(viewModalElement);
    const submitButton = form.querySelector('[type="submit"]');

    // Función centralizada para fetch
    async function fetchSeguro(url, options) {
        const response = await fetch(url, options);
        if (!response.ok) {
            let errorMessage = 'Ocurrió un error en el servidor';
            try {
                const data = await response.json();
                errorMessage = data.message || errorMessage;
            } catch (e) {
                console.error('El servidor no devolvió JSON válido', e);
            }
            throw new Error(errorMessage);
        }
        return await response.json();
    }

    // Inicialización de FilePond 
    const pond = window.FilePond.create(document.querySelector('input[name="foto"]'),
    {
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp'],
        maxFileSize: '2MB',
        labelIdle: 'Arrastra tu foto o <span class="filepond--label-action">Busca en tu PC</span>',

        server: {
                process: '/filepond',
                revert: '/filepond',

                load: (source, load, error, progress, abort) => {
                    const controller = new AbortController();

                    fetch(source, { signal: controller.signal })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('No se pudo cargar la imagen');
                            }
                            return response.blob();
                        })
                        .then(load)
                        .catch(err => {
                            if (err.name !== 'AbortError') {
                                error(err.message);
                            }
                        });

                    return {
                        abort: () => {
                            controller.abort();
                            abort();
                        }
                    };
                },

                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            }
        }
    );

    // Inicialización de DataTables
    const table = new window.DataTable('#tbl_empleados', {
        ajax: "/empleados/listar", 
        columns: [
            { 
                data: null,
                title: 'Opciones',
                orderable: false,
                render: function(data, type, row) {
                    const idSeguro = parseInt(row.id); 
                    return `
                        <button class="btn btn-primary btn-sm btn-ver me-1" data-id="${idSeguro}" title="Ver">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm btn-editar me-1" data-id="${idSeguro}" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="${idSeguro}" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            },
            { 
                data: 'foto', 
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
                render: function(data) { return `${data.nombre} ${data.apellido}`; }
            },
            { data: 'dui', title: 'N° DUI' },
            { data: 'correo', title: 'Correo' },
            { data: 'telefono', title: 'Teléfono' },
            { data: 'direccion', title: 'Dirección' },
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

    // Eventos de interfaz
    document.getElementById('btn_nuevo_empleado').addEventListener('click', function() {
        form.reset(); 
        document.getElementById('idempleado').value = ''; 
        modalTitle.textContent = 'Nuevo Empleado'; 
        submitButton.textContent = 'Guardar Empleado';
        
        pond.removeFiles();
        
        modalInstance.show();
    });

    //Evento para el botón de editar
    document.getElementById('tbl_empleados').addEventListener('click', async function(e) {
        const btn = e.target.closest('.btn-editar');
        if (!btn) return;
        
        const id = btn.dataset.id;
        
        try {
            const response = await fetch(`/empleados/${id}`, { headers: { 'Accept': 'application/json' } });
            const data = await response.json();
            
            document.getElementById('idempleado').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('apellido').value = data.apellido;
            document.getElementById('dui').value = data.dui;
            document.getElementById('correo').value = data.correo;
            document.getElementById('telefono').value = data.telefono;
            document.getElementById('direccion').value = data.direccion;
            document.getElementById('activo').value = data.activo;

            pond.removeFiles();

            if (data.foto) {
                pond.addFile(data.foto, { type: 'local' });
            }
            
            modalTitle.textContent = 'Editar Empleado';
            submitButton.textContent = 'Actualizar Empleado';
            
            modalInstance.show();
            
        } catch (error) {
            Swal.fire('Error', 'No se pudieron cargar los datos del empleado', 'error');
        }
    });

    // Evento para el botón de ver
    document.getElementById('tbl_empleados').addEventListener('click', async function(e) {
        const btnVer = e.target.closest('.btn-ver');
        if (btnVer) {
            const id = btnVer.dataset.id;
            try {
                const response = await fetch(`/empleados/${id}`, { headers: { 'Accept': 'application/json' } });
                const data = await response.json();
                
                // Llenar los datos
                document.getElementById('ver_nombre_completo').textContent = `${data.nombre} ${data.apellido}`;
                document.getElementById('ver_dui').textContent = data.dui;
                document.getElementById('ver_correo').textContent = data.correo;
                document.getElementById('ver_telefono').textContent = data.telefono;
                document.getElementById('ver_direccion').textContent = data.direccion;
                
                // Estado con color
                document.getElementById('ver_estado').innerHTML = data.activo == 1 
                    ? '<span class="badge bg-success">Activado</span>' 
                    : '<span class="badge bg-danger">Desactivado</span>';

                // Manejo de la foto
                const imgFoto = document.getElementById('ver_foto');
                const divSinFoto = document.getElementById('ver_sin_foto');
                
                if (data.foto) {
                    imgFoto.src = data.foto;
                    imgFoto.classList.remove('d-none');
                    divSinFoto.classList.add('d-none');
                } else {
                    imgFoto.src = '';
                    imgFoto.classList.add('d-none');
                    divSinFoto.classList.remove('d-none');
                }

                viewModalInstance.show();
            } catch (error) {
                Swal.fire('Error', 'No se pudo cargar la información', 'error');
            }
            return; 
        }
    });

    // Máscara para el DUI 
    const inputDui = document.getElementById('dui');
    inputDui.addEventListener('input', function(e) {
        let valor = e.target.value.replace(/\D/g, ''); 
        if (valor.length > 9) valor = valor.slice(0, 9); 
        
        if (valor.length > 8) {
            valor = valor.slice(0, 8) + '-' + valor.slice(8);
        }
        e.target.value = valor;
    });

    // Máscara para el Teléfono (xxxx-xxxx, inicia con 2, 6 o 7)
    const inputTelefono = document.getElementById('telefono');
    inputTelefono.addEventListener('input', function(e) {
        let valor = e.target.value.replace(/\D/g, ''); 
        if (valor.length > 8) valor = valor.slice(0, 8); 
        if (valor.length > 0 && !['2', '6', '7'].includes(valor[0])) {
            valor = valor.slice(1); 
        }
        if (valor.length > 4) {
            valor = valor.slice(0, 4) + '-' + valor.slice(4);
        }
        e.target.value = valor;
    });

    // Operaciones HTTP
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('idempleado').value;
        const isEdit = id !== '';

        submitButton.disabled = true;
        const textoOriginal = submitButton.innerHTML;
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...`;

        const formData = new FormData(form);

        const archivos = pond.getFiles();
        const fotoNueva = archivos.some(file => file.origin === FilePond.FileOrigin.INPUT);

        if (!fotoNueva) {
            formData.delete('foto');
        }
        console.log('Foto en FormData:', formData.get('foto'));
        console.log('Estado de FilePond:', pond.getFiles().map(file => ({
            name: file.filename,
            source: file.source,
            type: file.origin,
            serverId: file.serverId,
            status: file.status
        })));

        if (isEdit) formData.append('_method', 'PUT');

        const url = isEdit ? `/empleados/${id}` : "/empleados";

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

    // Eliminar 
    document.getElementById('tbl_empleados').addEventListener('click', async function(e) {
        const btn = e.target.closest('.btn-eliminar');
        if (!btn) return;
        
        const id = btn.dataset.id;
        
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "El empleado será eliminado del sistema",
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

                const data = await fetchSeguro(`/empleados/${id}`, { 
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