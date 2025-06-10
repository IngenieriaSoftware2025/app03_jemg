<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;

//impar la clase ClienteController
use Controllers\ClienteController;
use Controllers\MarcaController;
use Controllers\UsuarioController;
use Controllers\LoginController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

//RUTAS LOGIN
$router->get('/login', [LoginController::class,'renderizarPagina']);

// get obtiene la vista de la apgina
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
$router->post('/usuarios/EliminarUsuario', [UsuarioController::class,'EliminarUsuario']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
