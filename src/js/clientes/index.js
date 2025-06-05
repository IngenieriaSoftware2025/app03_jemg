import { Dropdown } from "bootstrap"; //si utilizo dropdown en mi layaut  (Dropdown es un funcion interna del MVC)
import Swal from "sweetalert2"; //para utilizar las alertas
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones"; //(validarFormulario es un funcion interna del MVC)
import { lenguaje } from "../lenguaje"; //(lenguaje es un funcion interna del MVC)
import { error } from "jquery";

const FormClientes = document.getElementById('FormClientes');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnEliminar = document.getElementById('BtnEliminar');
const validarTelefono = document.getElementById('cliente_telefono');
const validarNit = document.getElementById('cliente_nit');

const validacionTelefono = () => {

    const cantidadDigitos = validarTelefono.value

    if (cantidadDigitos.length < 1) {

        validarTelefono.classList.remove('is-valid', 'is-invalid');

    } else {
        if (cantidadDigitos.length < 1) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice el número de telefono",
                text: "La cantidad de digitos debe de ser mayor o igual a 8 digitos",
                showConfirmButton: false,
                timer: 3000
            })

            validarTelefono.classList.remove('is-valid');
            validarTelefono.classList.add('is-invalid');

        } else {
            validarTelefono.classList.remove('is-invalid');
            validarTelefono.classList.add('is-valid');
        }
    }

}


function validandoNit() {
    const nit = cliente_nit.value.trim();

    let nd, add = 0;

    if (nd = /^(\d+)-?([\dkK])$/.exec(nit)) {
        nd[2] = (nd[2].toLowerCase() === 'k') ? 10 : parseInt(nd[2], 10);

        for (let i = 0; i < nd[1].length; i++) {
            add += ((((i - nd[1].length) * -1) + 1) * parseInt(nd[1][i], 10));
        }
        return ((11 - (add % 11)) % 11) === nd[2];
    } else {
        return false;
    }
}

const EsValidoNit = () => {
    validandoNit();

    if (validandoNit()) {
        cliente_nit.classList.add('is-valid');
        cliente_nit.classList.remove('is-invalid');
    } else {
        cliente_nit.classList.remove('is-valid');
        cliente_nit.classList.add('is-invalid');

        Swal.fire({
            position: "center",
            icon: "warning",
            title: "NIT INVALIDO",
            text: "El numero de nit ingresado es invalido",
            showConfirmButton: false,
            timer: 3000
        });

    }
}

const GuardarCliente = async (event) => {
    event.preventDefault(); //evita el envio del formulario
    BtnGuardar.disabled = false;


    if (!validarFormulario(FormClientes, ['cliente_id'])) {
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
    const body = new FormData(FormClientes);

    const url = '/app03_jemg/clientes/guardarCliente';
    const config = {
        method: 'POST',
        body
    }

    //tratar de guardar un cliente
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
            BuscarCliente();

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


const datatable = new DataTable('#TableClientes', {
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
            data: 'cliente_id',
            width: '%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { title: 'Nombre', data: 'cliente_nombres' },
        { title: 'Apellidos', data: 'cliente_apellidos' },
        { title: 'Correo', data: 'cliente_correo' },
        { title: 'Telefono', data: 'cliente_telefono' },
        { title: 'Nit', data: 'cliente_nit' },
        {
            title: 'Acciones',
            data: 'cliente_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-nombres="${row.cliente_nombres}"  
                         data-apellidos="${row.cliente_apellidos}"  
                         data-nit="${row.cliente_nit}"  
                         data-telefono="${row.cliente_telefono}"  
                         data-correo="${row.cliente_correo}"  
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

const BuscarCliente = async () =>{
    const url = '/app03_jemg/clientes/buscarCliente';
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

    document.getElementById('cliente_id').value = datos.id
    document.getElementById('cliente_nombres').value = datos.nombres
    document.getElementById('cliente_apellidos').value = datos.apellidos
    document.getElementById('cliente_nit').value = datos.nit
    document.getElementById('cliente_telefono').value = datos.telefono
    document.getElementById('cliente_correo').value = datos.correo

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    })

}

const limpiarTodo = () => {
    FormClientes.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');

}

const ModificarCliente = async (event) => {
    event.preventDefault(),
    BtnModificar.disabled = true;

    if (!validarFormulario(FormClientes, [''])) {
        Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
            BtnGuardar.disabled = false;        
    }

    const body = new FormData(FormClientes);

    const url = '/app03_jemg/clientes/modificarCliente';
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
            BuscarCliente();

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

const EliminarCliente = async (e) => {
    const idCliente = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: "Usted eliminara un cliente",
        showConfirmButton: true,
        confirmButtonText: "Si",
        confirmButtonColor: "red",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!AlertaConfirmarEliminar.isConfirmed) return;

    // Preparamos el body para POST
    const body = new URLSearchParams();
    body.append('cliente_id', idCliente);

    try {
        const respuesta = await fetch('/app03_jemg/clientes/EliminarCliente', {
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
            BuscarCliente();
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
BuscarCliente();
validarTelefono.addEventListener('change', validacionTelefono);
validarNit.addEventListener('change', EsValidoNit);

//guardar
FormClientes.addEventListener('submit', GuardarCliente)

//btn limpiar
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarCliente);

//datatable
datatable.on('click', '.eliminar', EliminarCliente);
datatable.on('click', '.modificar', llenarFormulario);


