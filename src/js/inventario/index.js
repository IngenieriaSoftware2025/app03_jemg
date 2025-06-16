import { Dropdown } from "bootstrap"; //si utilizo dropdown en mi layaut  (Dropdown es un funcion interna del MVC)
import Swal from "sweetalert2"; //para utilizar las alertas
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones"; //(validarFormulario es un funcion interna del MVC)
import { lenguaje } from "../lenguaje"; //(lenguaje es un funcion interna del MVC)
import { error } from "jquery";

const FormInventario = document.getElementById('FormInventario');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnEliminar = document.getElementById('BtnEliminar');
const validarStock = document.getElementById('inventario_stock_actual');
const validarPrecioVenta = document.getElementById('inventario_precio_venta');
const validarPrecioCompra = document.getElementById('inventario_precio_compra');

const validacionStock = () => {
    const stock = validarStock.value;

    if (stock.length < 1) {
        validarStock.classList.remove('is-valid', 'is-invalid');
    } else {
        if (parseInt(stock) < 0) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice el stock",
                text: "El stock no puede ser negativo",
                showConfirmButton: false,
                timer: 3000
            });

            validarStock.classList.remove('is-valid');
            validarStock.classList.add('is-invalid');
        } else {
            validarStock.classList.remove('is-invalid');
            validarStock.classList.add('is-valid');
        }
    }
}

const validacionPrecioVenta = () => {
    const precio = validarPrecioVenta.value;

    if (precio.length < 1) {
        validarPrecioVenta.classList.remove('is-valid', 'is-invalid');
    } else {
        if (parseFloat(precio) <= 0) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice el precio de venta",
                text: "El precio debe ser mayor a 0",
                showConfirmButton: false,
                timer: 3000
            });

            validarPrecioVenta.classList.remove('is-valid');
            validarPrecioVenta.classList.add('is-invalid');
        } else {
            validarPrecioVenta.classList.remove('is-invalid');
            validarPrecioVenta.classList.add('is-valid');
        }
    }
}

const validacionPrecioCompra = () => {
    const precio = validarPrecioCompra.value;

    if (precio.length < 1) {
        validarPrecioCompra.classList.remove('is-valid', 'is-invalid');
    } else {
        if (parseFloat(precio) <= 0) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revice el precio de compra",
                text: "El precio debe ser mayor a 0",
                showConfirmButton: false,
                timer: 3000
            });

            validarPrecioCompra.classList.remove('is-valid');
            validarPrecioCompra.classList.add('is-invalid');
        } else {
            validarPrecioCompra.classList.remove('is-invalid');
            validarPrecioCompra.classList.add('is-valid');
        }
    }
}

const CargarModelos = async () => {
    const url = '/app03_jemg/inventario/obtenerModelos';
    
    try {
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            const selectModelo = document.getElementById('modelo_id');
            
            // Limpiar opciones existentes
            selectModelo.innerHTML = '<option value="">Seleccione un modelo</option>';
            
            // Agregar modelos
            datos.data.forEach(modelo => {
                const option = new Option(modelo.modelo_completo, modelo.modelo_id);
                selectModelo.appendChild(option);
            });
        }
    } catch (error) {
        console.log('Error al cargar modelos:', error);
    }
}

const GuardarInventario = async (event) => {
    event.preventDefault(); //evita el envio del formulario
    BtnGuardar.disabled = false;

    if (!validarFormulario(FormInventario, ['inventario_id', 'inventario_precio_compra'])) {
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
    const body = new FormData(FormInventario);

    const url = '/app03_jemg/inventario/guardarInventario';
    const config = {
        method: 'POST',
        body
    }

    //tratar de guardar inventario
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
            BuscarInventario();

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

const datatable = new DataTable('#TableInventario', {
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
            data: 'inventario_id',
            width: '%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { title: 'Marca', data: 'marca_nombre' },
        { title: 'Modelo', data: 'modelo_nombre' },
        { 
            title: 'Stock', 
            data: 'inventario_stock_actual',
            render: (data, type, row, meta) => {
                const stock = parseInt(data);
                const color = stock <= 5 ? 'text-danger fw-bold' : stock <= 10 ? 'text-warning fw-bold' : 'text-success';
                return `<span class="${color}">${stock} unidades</span>`;
            }
        },
        { 
            title: 'Precio Venta', 
            data: 'inventario_precio_venta',
            render: (data, type, row, meta) => `Q. ${parseFloat(data).toFixed(2)}`
        },
        { 
            title: 'Precio Compra', 
            data: 'inventario_precio_compra',
            render: (data, type, row, meta) => data ? `Q. ${parseFloat(data).toFixed(2)}` : 'N/A'
        },
        { 
            title: 'Última Actualización', 
            data: 'inventario_fecha_actualizacion',
            render: (data, type, row, meta) => {
                if (data) {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                }
                return 'N/A';
            }
        },
        {
            title: 'Acciones',
            data: 'inventario_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-modelo="${row.modelo_id}"
                         data-stock="${row.inventario_stock_actual}"  
                         data-precio-venta="${row.inventario_precio_venta}"  
                         data-precio-compra="${row.inventario_precio_compra || ''}"  
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

const BuscarInventario = async () =>{
    const url = '/app03_jemg/inventario/buscarInventario';
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

    document.getElementById('inventario_id').value = datos.id
    document.getElementById('modelo_id').value = datos.modelo
    document.getElementById('inventario_stock_actual').value = datos.stock
    document.getElementById('inventario_precio_venta').value = datos.precioVenta
    document.getElementById('inventario_precio_compra').value = datos.precioCompra

    // Deshabilitar select de modelo en modificación
    document.getElementById('modelo_id').disabled = true;

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    })
}

const limpiarTodo = () => {
    FormInventario.reset();
    document.getElementById('modelo_id').disabled = false;
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
}

const ModificarInventario = async (event) => {
    event.preventDefault(),
    BtnModificar.disabled = true;

    if (!validarFormulario(FormInventario, ['inventario_precio_compra'])) {
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

    const body = new FormData(FormInventario);

    const url = '/app03_jemg/inventario/modificarInventario';
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
            BuscarInventario();

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

const EliminarInventario = async (e) => {
    const idInventario = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: "Usted eliminara un producto del inventario",
        showConfirmButton: true,
        confirmButtonText: "Si",
        confirmButtonColor: "red",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!AlertaConfirmarEliminar.isConfirmed) return;

    // Preparamos el body para POST
    const body = new URLSearchParams();
    body.append('inventario_id', idInventario);

    try {
        const respuesta = await fetch('/app03_jemg/inventario/eliminarInventario', {
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
            BuscarInventario();
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
CargarModelos();
BuscarInventario();
validarStock.addEventListener('change', validacionStock);
validarPrecioVenta.addEventListener('change', validacionPrecioVenta);
validarPrecioCompra.addEventListener('change', validacionPrecioCompra);

//guardar
FormInventario.addEventListener('submit', GuardarInventario)

//btn limpiar
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarInventario);

//datatable
datatable.on('click', '.eliminar', EliminarInventario);
datatable.on('click', '.modificar', llenarFormulario);