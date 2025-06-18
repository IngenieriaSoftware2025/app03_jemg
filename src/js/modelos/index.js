import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormModelos = document.getElementById('FormModelos');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');

const CargarMarcas = async () => {
    const url = '/app03_jemg/modelos/obtenerMarcas';
    
    try {
        const respuesta = await fetch(url);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            const selectMarca = document.getElementById('marca_id');
            
            // Limpiar opciones existentes
            selectMarca.innerHTML = '<option value="">Seleccione una marca</option>';
            
            // Agregar marcas
            datos.data.forEach(marca => {
                const option = new Option(marca.marca_nombre, marca.marca_id);
                selectMarca.appendChild(option);
            });
        }
    } catch (error) {
        console.log('Error al cargar marcas:', error);
    }
}

const GuardarModelo = async (event) => {
   event.preventDefault();
   BtnGuardar.disabled = true;

   if (!validarFormulario(FormModelos, ['modelo_id'])) {
       Swal.fire({
           position: "center",
           icon: "warning",
           title: "FORMULARIO INCOMPLETO",
           text: "Debe de validar todos los campos",
           showConfirmButton: false,
           timer: 3000
       });
       BtnGuardar.disabled = false;
       return;
   }

   const body = new FormData(FormModelos);
   const url = '/app03_jemg/modelos/guardarModelo';
   const config = { method: 'POST', body }

   try {
       const respuesta = await fetch(url, config);
       const datos = await respuesta.json();
       const { codigo, mensaje } = datos;

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
           BuscarModelo();
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
   }
   BtnGuardar.disabled = false;
}

const datatable = new DataTable('#TableModelos', {
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
           data: 'modelo_id',
           width: '5%',
           render: (data, type, row, meta) => meta.row + 1
       },
       { 
           title: 'Marca', 
           data: 'marca_nombre',
           width: '20%'
       },
       { 
           title: 'Modelo', 
           data: 'modelo_nombre',
           width: '25%'
       },
       { 
           title: 'Descripción', 
           data: 'modelo_descripcion',
           width: '30%',
           render: (data, type, row, meta) => {
               return data && data.length > 50 ? data.substring(0, 50) + '...' : data || 'Sin descripción';
           }
       },
       { 
           title: 'Precio', 
           data: 'modelo_precio_referencia',
           width: '10%',
           render: (data, type, row, meta) => {
               return data ? `Q ${parseFloat(data).toFixed(2)}` : 'No definido';
           }
       },
       {
           title: 'Acciones',
           data: 'modelo_id',
           width: '10%',
           searchable: false,
           orderable: false,
           render: (data, type, row, meta) => {
               return `
               <div class='d-flex justify-content-center'>
                    <button class='btn btn-warning btn-sm modificar mx-1' 
                        data-id="${data}" 
                        data-marca="${row.marca_id}"
                        data-nombre="${row.modelo_nombre}"  
                        data-descripcion="${row.modelo_descripcion || ''}"
                        data-precio="${row.modelo_precio_referencia || ''}"
                        title="Modificar">
                        <i class='bi bi-pencil'></i>
                    </button>
                    <button class='btn btn-danger btn-sm eliminar mx-1' 
                        data-id="${data}" title="Eliminar">
                       <i class="bi bi-trash3"></i>
                    </button>
                </div>`;
           }
       }
   ],
});

const BuscarModelo = async () => {
   const url = '/app03_jemg/modelos/buscarModelo';
   const config = { method: 'GET' }

   try {
       const respuesta = await fetch(url, config);
       const datos = await respuesta.json();
       const { codigo, mensaje, data } = datos;

       if (codigo === 1) {
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
       }
   } catch (error) {
       console.log(error);
   }
}

const llenarFormulario = (event) => {
   const datos = event.currentTarget.dataset;

   document.getElementById('modelo_id').value = datos.id;
   document.getElementById('marca_id').value = datos.marca;
   document.getElementById('modelo_nombre').value = datos.nombre;
   document.getElementById('modelo_descripcion').value = datos.descripcion;
   document.getElementById('modelo_precio_referencia').value = datos.precio;

   BtnGuardar.classList.add('d-none');
   BtnModificar.classList.remove('d-none');

   window.scrollTo({ top: 0 });
}

const limpiarTodo = () => {
   FormModelos.reset();
   BtnGuardar.classList.remove('d-none');
   BtnModificar.classList.add('d-none');
}

const ModificarModelo = async (event) => {
   event.preventDefault();
   BtnModificar.disabled = true;

   if (!validarFormulario(FormModelos, ['modelo_id'])) {
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

   const body = new FormData(FormModelos);
   const url = '/app03_jemg/modelos/modificarModelo';
   const config = { method: 'POST', body }

   try {
       const respuesta = await fetch(url, config);
       const datos = await respuesta.json();
       const { codigo, mensaje } = datos;

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
           BuscarModelo();
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
   }
   BtnModificar.disabled = false;
}

const EliminarModelo = async (e) => {
   const idModelo = e.currentTarget.dataset.id;

   const AlertaConfirmarEliminar = await Swal.fire({
       position: "center",
       icon: "info",
       title: "¿Desea ejecutar esta acción?",
       text: "Usted eliminará un modelo",
       showConfirmButton: true,
       confirmButtonText: "Sí",
       confirmButtonColor: "red",
       cancelButtonText: "Cancelar",
       showCancelButton: true
   });

   if (!AlertaConfirmarEliminar.isConfirmed) return;

   const body = new URLSearchParams();
   body.append('modelo_id', idModelo);

   try {
       const respuesta = await fetch('/app03_jemg/modelos/EliminarModelo', {
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
           BuscarModelo();
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

// Eventos
CargarMarcas();
BuscarModelo();

FormModelos.addEventListener('submit', GuardarModelo);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarModelo);

datatable.on('click', '.eliminar', EliminarModelo);
datatable.on('click', '.modificar', llenarFormulario);
