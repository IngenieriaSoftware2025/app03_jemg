import { Dropdown } from "bootstrap"; //si utilizo dropdown en mi layaut  (Dropdown es un funcion interna del MVC)
import Swal from "sweetalert2"; //para utilizar las alertas
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones"; //(validarFormulario es un funcion interna del MVC)
import { lenguaje } from "../lenguaje"; //(lenguaje es un funcion interna del MVC)
import { error } from "jquery";

const FormUsuarios = document.getElementById('FormUsuarios');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnEliminar = document.getElementById('BtnEliminar');

// Validaciones específicas para usuarios
const validarTelefono = document.getElementById('usuario_tel');
const validarDPI = document.getElementById('usuario_dpi');
const validarCorreo = document.getElementById('usuario_correo');
const confirmarContra = document.getElementById('confirmar_contra');

const BuscarUsuario = async () => {
    const url = '/app03_jemg/usuarios/buscarUsuario';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config)
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo === 1) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });

            datatable.clear().draw();
            datatable.rows.add(data).draw();
            
        } else {
            Swal.fire({
                position: "center",
                icon: "info",
                title: "Información",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
            return;
        }
    } catch (error) {
        console.log(error);
        
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
                         data-fotografia="${row.usuario_fotografia || ''}"  
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger eliminar mx-1' 
                         data-id="${data}">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ],
})

const GuardarUsuario = async (event) => {
    event.preventDefault(); //evita el envio del formulario
    BtnGuardar.disabled = true;

    // Validación de contraseñas coincidentes
    const password = document.getElementById('usuario_contra').value;
    const confirmPassword = document.getElementById('confirmar_contra').value;
    
    if (password !== confirmPassword) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "CONTRASEÑAS NO COINCIDEN",
            text: "Las contraseñas ingresadas no son iguales",
            showConfirmButton: false,
            timer: 3000
        });
        BtnGuardar.disabled = false;
        return;
    }

    if (!validarFormulario(FormUsuarios, ['usuario_id', 'usuario_nom2', 'usuario_ape2', 'usuario_fotografia'])) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe validar todos los campos obligatorios",
            showConfirmButton: false,
            timer: 3000
        });
        BtnGuardar.disabled = false;
        return;
    }

    //crea una instancia de la clase FormData
    const body = new FormData(FormUsuarios);

    const url = '/app03_jemg/usuarios/guardarUsuario';
    const config = {
        method: 'POST',
        body
    }

    //tratar de guardar un usuario
    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        const { codigo, mensaje } = datos
        console.log("Respuesta del servidor:", datos);
        
        if (codigo == 1) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });

            limpiarTodo();
            BuscarUsuario();

        } else {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
        }
    } catch (error) {
        console.log(error);
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: false,
            timer: 3000,
        });
    }
    BtnGuardar.disabled = false;
}

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

    // No llenar campos de contraseña por seguridad
    document.getElementById('usuario_contra').value = ''
    document.getElementById('confirmar_contra').value = ''

    // Mostrar información de fotografía actual si existe
    const fotoActual = datos.fotografia
    if (fotoActual) {
        // Crear elemento para mostrar foto actual (opcional)
        console.log('Fotografía actual:', fotoActual)
    }

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    })

}

const limpiarTodo = () => {
    FormUsuarios.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');

    // Limpiar campos de contraseña específicamente
    document.getElementById('usuario_contra').value = '';
    document.getElementById('confirmar_contra').value = '';
}

const ModificarUsuario = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    // No validar contraseñas en modificación (son opcionales)
    if (!validarFormulario(FormUsuarios, ['usuario_nom2', 'usuario_ape2', 'usuario_fotografia', 'usuario_contra', 'confirmar_contra'])) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe validar todos los campos obligatorios",
            showConfirmButton: false,
            timer: 3000,
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

        if (codigo === 1) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });

            limpiarTodo();
            BuscarUsuario();

        } else {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
        }

    } catch (error) {
        console.log(error);
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: false,
            timer: 3000,
        });
    }
    BtnModificar.disabled = false;
}

const EliminarUsuario = async (e) => {
    const idUsuario = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "warning",
        title: "¿Desea ejecutar esta acción?",
        text: "Usted eliminará un usuario del sistema",
        showConfirmButton: true,
        confirmButtonText: "Sí, eliminar",
        confirmButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!AlertaConfirmarEliminar.isConfirmed) return;

    // Preparamos el body para POST
    const body = new URLSearchParams();
    body.append('usuario_id', idUsuario);

    try {
        const respuesta = await fetch('/app03_jemg/usuarios/eliminarUsuario', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body
        });

        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: false,
                timer: 2000
            });
            BuscarUsuario();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000
            });
        }

    } catch (error) {
        console.log(error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: false,
            timer: 3000
        });
    }
};

//Eventos
BuscarUsuario();

// Validaciones en tiempo real
validarTelefono.addEventListener('input', function() {
    const telefono = this.value;
    if (telefono.length !== 8) {
        this.setCustomValidity('El teléfono debe tener exactamente 8 dígitos');
    } else {
        this.setCustomValidity('');
    }
});

validarDPI.addEventListener('input', function() {
    const dpi = this.value;
    if (dpi.length !== 13) {
        this.setCustomValidity('El DPI debe tener exactamente 13 dígitos');
    } else {
        this.setCustomValidity('');
    }
});

validarCorreo.addEventListener('input', function() {
    const correo = this.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(correo)) {
        this.setCustomValidity('Ingrese un correo electrónico válido');
    } else {
        this.setCustomValidity('');
    }
});

confirmarContra.addEventListener('input', function() {
    const password = document.getElementById('usuario_contra').value;
    const confirmPassword = this.value;
    if (password !== confirmPassword) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});

// Validar cuando se cambie la contraseña principal
document.getElementById('usuario_contra').addEventListener('input', function() {
    const password = this.value;
    const confirmPassword = confirmarContra.value;
    
    // Validar longitud mínima
    if (password.length < 8) {
        this.setCustomValidity('La contraseña debe tener al menos 8 caracteres');
    } else {
        this.setCustomValidity('');
    }
    
    // Revalidar confirmación si ya se llenó
    if (confirmPassword) {
        if (password !== confirmPassword) {
            confirmarContra.setCustomValidity('Las contraseñas no coinciden');
        } else {
            confirmarContra.setCustomValidity('');
        }
    }
});

//guardar
FormUsuarios.addEventListener('submit', GuardarUsuario)

//btn limpiar
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarUsuario);

//datatable
datatable.on('click', '.eliminar', EliminarUsuario);
datatable.on('click', '.modificar', llenarFormulario);