<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Clientes;

//C:\docker\app03_jemg\views\clientes\index.php
class ClienteController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('clientes/index', []);
    }

    public static function guardarCliente()
    {
        getHeadersApi();

        //saniticacion de nombre y validaccion con capital
        $_POST['cliente_nombres'] = ucwords(strtolower(trim(htmlspecialchars($_POST['cliente_nombres']))));

        $cantidad_nombre = strlen($_POST['cliente_nombres']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre debe de tener mas de 1 caracteres'
            ]);
            return;
        }


        //saniticacion de apellido y validaccion con capital
        $_POST['cliente_apellidos'] = ucwords(strtolower(trim(htmlspecialchars($_POST['cliente_apellidos']))));
        $cantidad_apellido = strlen($_POST['cliente_apellidos']);

        if ($cantidad_apellido < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Nombre debe de tener mas de 1 caracteres'
            ]);
            return;
        }

        $_POST['cliente_nit'] = filter_var($_POST['cliente_nit'], FILTER_SANITIZE_NUMBER_INT);

        $_POST['cliente_telefono'] = filter_var($_POST['cliente_telefono'], FILTER_SANITIZE_NUMBER_INT);
        if (strlen($_POST['cliente_telefono']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El telefono debe de tener 8 numeros'
            ]);
            return;
        }

        $_POST['cliente_correo'] = filter_var($_POST['cliente_correo'], FILTER_SANITIZE_EMAIL);

        //se envian los datos a guardar despues de sanitizar
        try {
            $cliente = new Clientes(
                [
                    'cliente_nombres' => $_POST['cliente_nombres'],
                    'cliente_apellidos' => $_POST['cliente_apellidos'],
                    'cliente_nit' => $_POST['cliente_nit'],
                    'cliente_telefono' => $_POST['cliente_telefono'],
                    'cliente_correo' => $_POST['cliente_correo'],
                    'cliente_situacion' => 1

                ]
            );

            $crear = $cliente->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Exito al guardar cliente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Erro al guardar cliente',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
