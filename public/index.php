<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;

//impar la clase ClienteController
use Controllers\ClienteController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

// get obtiene la vista de la apgina
$router->get('/index', [ClienteController::class,'renderizarPagina']);
$router->post('/clientes/guardarCliente', [ClienteController::class,'guardarCliente']);
$router->get('/clientes/buscarCliente', [ClienteController::class,'buscarCliente']);
$router->post('/clientes/modificarCliente', [ClienteController::class,'modificarCliente']);
$router->post('/clientes/EliminarCliente', [ClienteController::class,'EliminarCliente']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
