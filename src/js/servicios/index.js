import { Dropdown } from "bootstrap"; //si utilizo dropdown en mi layaut  (Dropdown es un funcion interna del MVC)
import Swal from "sweetalert2"; //para utilizar las alertas
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones"; //(validarFormulario es un funcion interna del MVC)
import { lenguaje } from "../lenguaje"; //(lenguaje es un funcion interna del MVC)
import { error } from "jquery";

const FormServicios = document.getElementById('FormServicios');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnEliminar = document.getElementById('BtnEliminar');
const validarPrecio = document.getElementById('servicio_precio');
const validarTiempo = document.getElementById('servicio_tiempo_estimado');

const validacionPrecio = () => {
    const precio = validarPrecio.value;

    if (precio.length < 1) {
        validarPrecio.classList.remove('is-valid', 'is-invalid');
    } else {
        if (parseFloat(precio) <= 0) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice el precio",
                text: "El precio debe ser mayor a 0",
                showConfirmButton: false,
                timer: 3000
            });

            validarPrecio.classList.remove('is-valid');
            validarPrecio.classList.add('is-invalid');
        } else {
            validarPrecio.classList.remove('is-invalid');
            validarPrecio.classList.add('is-valid');
        }
    }
}

const validacionTiempo = () => {
    const tiempo = validarTiempo.value;

    if (tiempo.length < 1) {
        validarTiempo.classList.remove('is-valid', 'is-invalid');
    } else {
        if (parseInt(tiempo) <= 0) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice el tiempo estimado",
                text: "El tiempo debe ser mayor a 0 horas",
                showConfirmButton: false,
                timer: 3000
            });

            validarTiempo.classList.remove('is-valid');
            validarTiempo.classList.add('is-invalid');
        } else {
            validarTiempo.classList.remove('is-invalid');
            validarTiempo.classList.add('is-valid');
        }
    }
}

const GuardarServicio = async (event) => {
    event.preventDefault(); //evita el envio del formulario
    BtnGuardar.disabled = false;

    if (!validarFormulario(FormServicios, ['servicio_id', 'servicio_descripcion', 'servicio_tiempo_estimado'])) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos obligatorios",
            showConfirmButton: false,
            timer: 3000
        });
        return;
    }

    //crea una instancia de la clase FormData
    const body = new FormData(FormServicios);

    const url = '/app03_jemg/servicios/guardarServicio';
    const config = {
        method: 'POST',
        body
    }

    //tratar de guardar un servicio
    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        const { codigo, mensaje } = datos
        console.log("Respuesta del servidor:", datos);
        if (codigo == 1) {

            Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });

            limpiarTodo();
            BuscarServicio();

        } else {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
            return;
        }
    } catch (error) {
        console.log(error)
    }
    BtnGuardar.disabled = false;
}

const datatable = new DataTable('#TableServicios', {
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
            data: 'servicio_id',
            width: '%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { title: 'Nombre', data: 'servicio_nombre' },
        { title: 'Descripción', data: 'servicio_descripcion' },
        { 
            title: 'Precio', 
            data: 'servicio_precio',
            render: (data, type, row, meta) => `Q. ${parseFloat(data).toFixed(2)}`
        },
        { 
            title: 'Tiempo (hrs)', 
            data: 'servicio_tiempo_estimado',
            render: (data, type, row, meta) => data ? `${data} hrs` : 'N/A'
        },
        {
            title: 'Acciones',
            data: 'servicio_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-nombre="${row.servicio_nombre}"  
                         data-descripcion="${row.servicio_descripcion || ''}"  
                         data-precio="${row.servicio_precio}"  
                         data-tiempo="${row.servicio_tiempo_estimado || ''}"  
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

const BuscarServicio = async () =>{
    const url = '/app03_jemg/servicios/buscarServicio';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config)
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo ===1) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });

            datatable.clear().draw();
            datatable.rows.add(data).draw();
            
        } else {
            Swal.fire({
                position: "center",
                icon: "Info",
                title: "Error",
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

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('servicio_id').value = datos.id
    document.getElementById('servicio_nombre').value = datos.nombre
    document.getElementById('servicio_descripcion').value = datos.descripcion
    document.getElementById('servicio_precio').value = datos.precio
    document.getElementById('servicio_tiempo_estimado').value = datos.tiempo

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    })
}

const limpiarTodo = () => {
    FormServicios.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
}

const ModificarServicio = async (event) => {
    event.preventDefault(),
    BtnModificar.disabled = true;

    if (!validarFormulario(FormServicios, ['servicio_descripcion', 'servicio_tiempo_estimado'])) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos obligatorios",
            showConfirmButton: false,
            timer: 3000,
        });
        BtnModificar.disabled = false;
        return;        
    }

    const body = new FormData(FormServicios);

    const url = '/app03_jemg/servicios/modificarServicio';
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
                title: "Exito",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });

            limpiarTodo();
            BuscarServicio();

        } else {
            Swal.fire({
                position: "center",
                icon: "Info",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
            return;
        }

    } catch (error) {
        console.log(error);
    }
    BtnModificar.disabled = false;
}

const EliminarServicio = async (e) => {
    const idServicio = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: "Usted eliminara un servicio",
        showConfirmButton: true,
        confirmButtonText: "Si",
        confirmButtonColor: "red",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!AlertaConfirmarEliminar.isConfirmed) return;

    // Preparamos el body para POST
    const body = new URLSearchParams();
    body.append('servicio_id', idServicio);

    try {
        const respuesta = await fetch('/app03_jemg/servicios/eliminarServicio', {
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
                text: mensaje
            });
            BuscarServicio();
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 1000
            });
        }

    } catch (error) {
        console.log(error);
    }
};

//Eventos
BuscarServicio();
validarPrecio.addEventListener('change', validacionPrecio);
validarTiempo.addEventListener('change', validacionTiempo);

//guardar
FormServicios.addEventListener('submit', GuardarServicio)

//btn limpiar
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarServicio);

//datatable
datatable.on('click', '.eliminar', EliminarServicio);
datatable.on('click', '.modificar', llenarFormulario);