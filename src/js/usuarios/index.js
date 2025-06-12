import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import { data } from "jquery";

const FormUsuarios = document.getElementById('FormUsuarios');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');

const usuario_contrasena = document.getElementById('usuario_contrasena');
const confirmar_contra = document.getElementById('confirmar_contra');
const usuario_fotografia = document.getElementById('usuario_fotografia');

// Usando tu estilo de nomenclatura
const InputUsuarioTelefono = document.getElementById('usuario_tel');
const InputUsuarioDPI = document.getElementById('usuario_dpi');

const ValidarTelefono = () => {
    const CantidadDigitos = InputUsuarioTelefono.value

    if (CantidadDigitos.length < 1) {
        InputUsuarioTelefono.classList.remove('is-valid', 'is-invalid');
    } else {
        if (CantidadDigitos.length != 8) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Revise el numero de telefono",
                text: "La cantidad de digitos debe ser igual a 8 digitos",
                showConfirmButton: true,
            });

            InputUsuarioTelefono.classList.remove('is-valid');
            InputUsuarioTelefono.classList.add('is-invalid');
        } else {
            InputUsuarioTelefono.classList.remove('is-invalid');
            InputUsuarioTelefono.classList.add('is-valid');
        }
    }
}

const ValidarDPI = () => {
    const CantidadDigitos = InputUsuarioDPI.value

    if (CantidadDigitos.length < 1) {
        InputUsuarioDPI.classList.remove('is-valid', 'is-invalid');
    } else {
        if (CantidadDigitos.length != 13) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Revise el numero de DPI",
                text: "La cantidad de digitos debe ser igual a 13 digitos",
                showConfirmButton: true,
            });

            InputUsuarioDPI.classList.remove('is-valid');
            InputUsuarioDPI.classList.add('is-invalid');
        } else {
            InputUsuarioDPI.classList.remove('is-invalid');
            InputUsuarioDPI.classList.add('is-valid');
        }
    }
}

const ValidarContrasenas = () => {
    const password = usuario_contrasena.value;
    const confirmPassword = confirmar_contra.value;
    
    // Validar longitud mínima
    if (password.length > 0 && password.length < 8) {
        usuario_contrasena.classList.remove('is-valid');
        usuario_contrasena.classList.add('is-invalid');
        Swal.fire({
            position: "center",
            icon: "error",
            title: "CONTRASEÑA MUY CORTA",
            text: "La contraseña debe tener al menos 8 caracteres",
            showConfirmButton: true,
        });
        return false;
    } else if (password.length >= 8) {
        usuario_contrasena.classList.remove('is-invalid');
        usuario_contrasena.classList.add('is-valid');
    }
    
    // Validar que coincidan
    if (confirmPassword.length > 0 && password !== confirmPassword) {
        confirmar_contra.classList.remove('is-valid');
        confirmar_contra.classList.add('is-invalid');
        Swal.fire({
            position: "center",
            icon: "error",
            title: "CONTRASEÑAS NO COINCIDEN",
            text: "Las contraseñas ingresadas no son iguales",
            showConfirmButton: true,
        });
        return false;
    } else if (password === confirmPassword && confirmPassword.length >= 8) {
        confirmar_contra.classList.remove('is-invalid');
        confirmar_contra.classList.add('is-valid');
        return true;
    }
    
    return true;
}

const ValidarFotografia = () => {
    if (usuario_fotografia.files.length > 0) {
        const file = usuario_fotografia.files[0];
        const fileSize = file.size;
        const fileExtension = file.name.split('.').pop().toLowerCase();
        const allowedExtensions = ['jpg', 'jpeg', 'png'];
        
        // Validar extensión
        if (!allowedExtensions.includes(fileExtension)) {
            usuario_fotografia.classList.remove('is-valid');
            usuario_fotografia.classList.add('is-invalid');
            Swal.fire({
                position: "center",
                icon: "error",
                title: "FORMATO NO VÁLIDO",
                text: "Solo puede cargar archivos JPG, PNG o JPEG",
                showConfirmButton: true,
            });
            usuario_fotografia.value = '';
            return false;
        }
        
        // Validar tamaño (2MB)
        if (fileSize >= 2000000) {
            usuario_fotografia.classList.remove('is-valid');
            usuario_fotografia.classList.add('is-invalid');
            Swal.fire({
                position: "center",
                icon: "error",
                title: "ARCHIVO MUY GRANDE",
                text: "La imagen debe pesar menos de 2MB",
                showConfirmButton: true,
            });
            usuario_fotografia.value = '';
            return false;
        }
        
        // Si pasa todas las validaciones
        usuario_fotografia.classList.remove('is-invalid');
        usuario_fotografia.classList.add('is-valid');
        return true;
    }
    
    // Si no hay archivo seleccionado (es opcional)
    usuario_fotografia.classList.remove('is-valid', 'is-invalid');
    return true;
}

const GuardarUsuario = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    // Validar contraseñas antes del envío
    if (!ValidarContrasenas()) {
        BtnGuardar.disabled = false;
        return;
    }

    if (!validarFormulario(FormUsuarios, ['usuario_id', 'usuario_nom2', 'usuario_ape2', 'usuario_fotografia', 'usuario_fecha_contra'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos obligatorios",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormUsuarios);

    const url = '/app03_jemg/usuarios/guardarUsuario';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        console.log(datos)
        const { codigo, mensaje } = datos

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarUsuarios();
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error)
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: true,
        });
    }
    BtnGuardar.disabled = false;
}

const BuscarUsuarios = async () => {
    const url = '/app03_jemg/usuarios/buscarUsuario';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            // Mostrar alerta de éxito
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            // Cargar datos en el DataTable
            datatable.clear().draw();
            datatable.rows.add(data).draw();
        } else {
            // Mostrar alerta cuando no hay datos
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Información",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error);
        // Mostrar alerta de error de conexión
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: true,
        });
    }
}

const datatable = new DataTable('#TableUsuarios', {
    dom: `
        <"row mt-3 justify-content-between" 
            <"col" l> 
            <"col" B> 
            <"col-3" f>
        >
        t
        <"row mt-3 justify-content-between" 
            <"col-md-3 d-flex align-items-center" i> 
            <"col-md-8 d-flex justify-content-end" p>
        >
    `,
    language: lenguaje,
    data: [],
    columns: [
        {
            title: 'No.',
            data: 'usuario_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Nombre Completo', 
            data: 'usuario_nom1',
            render: (data, type, row) => {
                const nombre2 = row.usuario_nom2 ? ` ${row.usuario_nom2}` : '';
                const apellido2 = row.usuario_ape2 ? ` ${row.usuario_ape2}` : '';
                return `${row.usuario_nom1}${nombre2} ${row.usuario_ape1}${apellido2}`;
            }
        },
        { title: 'DPI', data: 'usuario_dpi' },
        { title: 'Teléfono', data: 'usuario_tel' },
        { title: 'Correo', data: 'usuario_correo' },
        { 
            title: 'Rol', 
            data: 'usuario_rol',
            render: (data, type, row) => {
                const badgeClass = data === 'ADMINISTRADOR' ? 'bg-primary' : 'bg-secondary';
                return `<span class="badge ${badgeClass}">${data}</span>`;
            }
        },
        { 
            title: 'Puesto', 
            data: 'usuario_puesto',
            render: (data, type, row) => {
                const puestoFormateado = data.replace(/_/g, ' ');
                return puestoFormateado;
            }
        },
        { 
            title: 'Estado', 
            data: 'usuario_estado',
            render: (data, type, row) => {
                let badgeClass = '';
                switch(data) {
                    case 'ACTIVO':
                        badgeClass = 'bg-success';
                        break;
                    case 'INACTIVO':
                        badgeClass = 'bg-warning';
                        break;
                    case 'SUSPENDIDO':
                        badgeClass = 'bg-danger';
                        break;
                    default:
                        badgeClass = 'bg-secondary';
                }
                return `<span class="badge ${badgeClass}">${data}</span>`;
            }
        },
        { 
            title: 'Fecha Contratación', 
            data: 'usuario_fecha_contra',
            render: (data, type, row) => {
                if (data) {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                } else {
                    return '<span class="text-muted">No especificada</span>';
                }
            }
        },
        { 
            title: 'Fotografía', 
            data: 'usuario_fotografia',
            orderable: false,
            render: (data, type, row) => {
                if (data) {
                    return `<img src="${data}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" alt="Foto">`;
                } else {
                    return `<i class="bi bi-person-circle" style="font-size: 40px; color: #6c757d;"></i>`;
                }
            }
        },
        {
            title: 'Acciones',
            data: 'usuario_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-nom1="${row.usuario_nom1}"  
                         data-nom2="${row.usuario_nom2 || ''}"  
                         data-ape1="${row.usuario_ape1}"  
                         data-ape2="${row.usuario_ape2 || ''}"  
                         data-tel="${row.usuario_tel}"  
                         data-direc="${row.usuario_direc}"  
                         data-dpi="${row.usuario_dpi}"  
                         data-correo="${row.usuario_correo}"  
                         data-rol="${row.usuario_rol}"  
                         data-puesto="${row.usuario_puesto}"  
                         data-estado="${row.usuario_estado}"  
                         data-fecha-contra="${row.usuario_fecha_contra || ''}"  
                         data-fotografia="${row.usuario_fotografia || ''}"  
                         >
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger eliminar mx-1' 
                         data-id="${data}">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('usuario_id').value = datos.id
    document.getElementById('usuario_nom1').value = datos.nom1
    document.getElementById('usuario_nom2').value = datos.nom2
    document.getElementById('usuario_ape1').value = datos.ape1
    document.getElementById('usuario_ape2').value = datos.ape2
    document.getElementById('usuario_tel').value = datos.tel
    document.getElementById('usuario_direc').value = datos.direc
    document.getElementById('usuario_dpi').value = datos.dpi
    document.getElementById('usuario_correo').value = datos.correo
    document.getElementById('usuario_rol').value = datos.rol
    document.getElementById('usuario_puesto').value = datos.puesto
    document.getElementById('usuario_estado').value = datos.estado

    // Manejar fecha de contratación
    if (datos.fechaContra) {
        const fecha = new Date(datos.fechaContra);
        const fechaFormateada = fecha.toISOString().slice(0, 16);
        document.getElementById('usuario_fecha_contra').value = fechaFormateada;
    } else {
        document.getElementById('usuario_fecha_contra').value = '';
    }

    // No llenar campos de contraseña por seguridad
    document.getElementById('usuario_contrasena').value = ''
    document.getElementById('confirmar_contra').value = ''

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    });
}

const limpiarTodo = () => {
    FormUsuarios.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    
    // Limpiar validaciones visuales
    document.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
        el.classList.remove('is-valid', 'is-invalid');
    });
}

const ModificarUsuario = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormUsuarios, ['usuario_nom2', 'usuario_ape2', 'usuario_fotografia', 'usuario_contrasena', 'confirmar_contra', 'usuario_fecha_contra'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos obligatorios",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormUsuarios);

    const url = '/app03_jemg/usuarios/modificarUsuario';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarUsuarios();
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }
    } catch (error) {
        console.log(error)
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: true,
        });
    }
    BtnModificar.disabled = false;
}

const EliminarUsuarios = async (e) => {
    const idUsuario = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "warning",
        title: "¿Desea ejecutar esta acción?",
        text: 'Esta completamente seguro que desea eliminar este registro',
        showConfirmButton: true,
        confirmButtonText: 'Si, Eliminar',
        confirmButtonColor: 'red',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        // Preparamos el body para POST
        const body = new URLSearchParams();
        body.append('usuario_id', idUsuario);

        try {
            const consulta = await fetch('/app03_jemg/usuarios/eliminarUsuario', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body
            });

            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Exito",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarUsuarios();
            } else {
                await Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Error",
                    text: mensaje,
                    showConfirmButton: true,
                });
            }
        } catch (error) {
            console.log(error)
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Error de conexión",
                text: "No se pudo conectar con el servidor",
                showConfirmButton: true,
            });
        }
    }
}

// Inicialización y eventos
BuscarUsuarios();
datatable.on('click', '.eliminar', EliminarUsuarios);
datatable.on('click', '.modificar', llenarFormulario);
FormUsuarios.addEventListener('submit', GuardarUsuario);
InputUsuarioTelefono.addEventListener('change', ValidarTelefono);
InputUsuarioDPI.addEventListener('change', ValidarDPI);
usuario_contrasena.addEventListener('plur', ValidarContrasenas);
confirmar_contra.addEventListener('plur', ValidarContrasenas);
usuario_fotografia.addEventListener('change', ValidarFotografia);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarUsuario);