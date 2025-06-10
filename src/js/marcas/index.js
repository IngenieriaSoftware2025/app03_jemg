import { Dropdown } from "bootstrap"; //si utilizo dropdown en mi layaut  (Dropdown es un funcion interna del MVC)
import Swal from "sweetalert2"; //para utilizar las alertas
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones"; //(validarFormulario es un funcion interna del MVC)
import { lenguaje } from "../lenguaje"; //(lenguaje es un funcion interna del MVC)
import { error } from "jquery";

const FormMarcas = document.getElementById('FormMarcas');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnEliminar = document.getElementById('BtnEliminar');

const GuardarMarca = async (event) => {
    event.preventDefault(); //evita el envio del formulario
    BtnGuardar.disabled = false;

    if (!validarFormulario(FormMarcas, ['marca_id'])) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: false,
            timer: 3000
        });
        return;
    }

    //crea una instancia de la clase FormData
    const body = new FormData(FormMarcas);

    const url = '/app03_jemg/marcas/guardarMarca';
    const config = {
        method: 'POST',
        body
    }

    //tratar de guardar una marca
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
            BuscarMarca();

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

const datatable = new DataTable('#TableMarcas', {
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
            data: 'marca_id',
            width: '%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { title: 'Nombre de la Marca', data: 'marca_nombre' },
        { title: 'Descripción', data: 'marca_descripcion' },
        {
            title: 'Acciones',
            data: 'marca_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-nombre="${row.marca_nombre}"  
                         data-descripcion="${row.marca_descripcion}"  
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

const BuscarMarca = async () =>{
    const url = '/app03_jemg/marcas/buscarMarca';
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

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('marca_id').value = datos.id
    document.getElementById('marca_nombre').value = datos.nombre
    document.getElementById('marca_descripcion').value = datos.descripcion

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    })

}

const limpiarTodo = () => {
    FormMarcas.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');

}

const ModificarMarca = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormMarcas, [''])) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: false,
            timer: 3000,
        });
        BtnModificar.disabled = false;
        return;       
    }

    const body = new FormData(FormMarcas);

    const url = '/app03_jemg/marcas/modificarMarca';
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
            BuscarMarca();

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
        console.log(error);
    }
    BtnModificar.disabled = false;
}

const EliminarMarca = async (e) => {
    const idMarca = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: "Usted eliminará una marca",
        showConfirmButton: true,
        confirmButtonText: "Sí",
        confirmButtonColor: "red",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!AlertaConfirmarEliminar.isConfirmed) return;

    // Preparamos el body para POST
    const body = new URLSearchParams();
    body.append('marca_id', idMarca);

    try {
        const respuesta = await fetch('/app03_jemg/marcas/EliminarMarca', {
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
            BuscarMarca();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
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
BuscarMarca();

//guardar
FormMarcas.addEventListener('submit', GuardarMarca)

//btn limpiar
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarMarca);

//datatable
datatable.on('click', '.eliminar', EliminarMarca);
datatable.on('click', '.modificar', llenarFormulario);