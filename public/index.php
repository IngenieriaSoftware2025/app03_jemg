<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;

//impar la clase ClienteController
use Controllers\ClienteController;
use Controllers\MarcaController;
use Controllers\UsuarioController;
use Controllers\LoginController;
use Controllers\ModeloController;
use Controllers\ServicioController;
use Controllers\InventarioController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

//RUTAS LOGIN
$router->get('/login', [LoginController::class,'renderizarPagina']);

// get obtiene la vista de la apgina
//rutas para clientes
$router->get('/clientes/index', [ClienteController::class,'renderizarPagina']);
$router->post('/clientes/guardarCliente', [ClienteController::class,'guardarCliente']);
$router->get('/clientes/buscarCliente', [ClienteController::class,'buscarCliente']);
$router->post('/clientes/modificarCliente', [ClienteController::class,'modificarCliente']);
$router->post('/clientes/EliminarCliente', [ClienteController::class,'EliminarCliente']);

//rutas para marcas
$router->get('/marcas/index', [MarcaController::class,'renderizarPagina']);
$router->post('/marcas/guardarMarca', [MarcaController::class,'guardarMarca']);
$router->get('/marcas/buscarMarca', [MarcaController::class,'buscarMarca']);
$router->post('/marcas/modificarMarca', [MarcaController::class,'modificarMarca']);
$router->post('/marcas/EliminarMarca', [MarcaController::class,'EliminarMarca']);

//rutas para usuarios
$router->get('/usuarios/index', [UsuarioController::class,'renderizarPagina']);
$router->post('/usuarios/guardarUsuario', [UsuarioController::class,'guardarUsuario']);
$router->get('/usuarios/buscarUsuario', [UsuarioController::class,'buscarUsuario']);
$router->post('/usuarios/modificarUsuario', [UsuarioController::class,'modificarUsuario']);
$router->post('/usuarios/eliminarUsuario', [UsuarioController::class,'eliminarUsuario']);

//rutas para modelos
$router->get('/modelos/index', [ModeloController::class,'renderizarPagina']);
$router->get('/modelos/obtenerMarcas', [ModeloController::class,'obtenerMarcas']);
$router->post('/modelos/guardarModelo', [ModeloController::class,'guardarModelo']);
$router->get('/modelos/buscarModelo', [ModeloController::class,'buscarModelo']);
$router->post('/modelos/modificarModelo', [ModeloController::class,'modificarModelo']);
$router->post('/modelos/EliminarModelo', [ModeloController::class,'EliminarModelo']);


//rutas para servicios
$router->get('/servicios/index', [ServicioController::class,'renderizarPagina']);
$router->post('/servicios/guardarServicio', [ServicioController::class,'guardarServicio']);
$router->get('/servicios/buscarServicio', [ServicioController::class,'buscarServicio']);
$router->post('/servicios/modificarServicio', [ServicioController::class,'modificarServicio']);
$router->post('/servicios/eliminarServicio', [ServicioController::class,'eliminarServicio']);


//rutas para inventario
$router->get('/inventario/index', [InventarioController::class,'renderizarPagina']);
$router->get('/inventario/obtenerModelos', [InventarioController::class,'obtenerModelos']);
$router->post('/inventario/guardarInventario', [InventarioController::class,'guardarInventario']);
$router->get('/inventario/buscarInventario', [InventarioController::class,'buscarInventario']);
$router->post('/inventario/modificarInventario', [InventarioController::class,'modificarInventario']);
$router->post('/inventario/eliminarInventario', [InventarioController::class,'eliminarInventario']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
