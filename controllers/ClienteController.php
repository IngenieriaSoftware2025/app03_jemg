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
                'mensaje' => 'Apellido debe de tener mas de 1 caracteres'
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

        if (!filter_var($_POST['cliente_correo'], FILTER_VALIDATE_EMAIL)){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electronico no es valido'
            ]);
            return;
        }

        // NUEVA VALIDACIÓN: Verificar NIT único
        try {
            $sql = "SELECT COUNT(*) as total FROM clientes 
                    WHERE cliente_nit = " . self::$db->quote($_POST['cliente_nit']) . "
                    AND cliente_situacion = 1";
            
            $resultado = self::fetchFirst($sql);
            
            if ($resultado && $resultado['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un cliente registrado con ese NIT'
                ]);
                return;
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al verificar NIT existente',
                'detalle' => $e->getMessage()
            ]);
            return;
        }

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
                'mensaje' => 'Error al guardar cliente',
                'detalle' => $e->getMessage()
            ]);
            return;
        }
    }

    public static function buscarCliente(){
        
        try {
            $sql = "SELECT * FROM clientes WHERE cliente_situacion = 1";
            $data = self::fetchArray($sql);

            if (count($data) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Clientes obtenidos correctamente',
                    'data' => $data

                ]);
            }else{
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al obtener clientes',
                    'detalle' => 'No hay clientes'
                ]);

            }

        } catch (Exception $e) {
            http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error en el servidor',
                    'detalle' => $e->getMessage()
                ]);
        }
    }

    public static function modificarCliente()
    {
        getHeadersApi();

        $id = $_POST['cliente_id'];

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
                'mensaje' => 'Apellido debe de tener mas de 1 caracteres'
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

        if (!filter_var($_POST['cliente_correo'], FILTER_VALIDATE_EMAIL)){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electronico no es valido'
            ]);
            return;
        }

        // NUEVA VALIDACIÓN: Verificar NIT único excluyendo cliente actual
        try {
            $sql = "SELECT COUNT(*) as total FROM clientes 
                    WHERE cliente_nit = " . self::$db->quote($_POST['cliente_nit']) . "
                    AND cliente_id != " . self::$db->quote($id) . "
                    AND cliente_situacion = 1";
            
            $resultado = self::fetchFirst($sql);
            
            if ($resultado && $resultado['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe otro cliente registrado con ese NIT'
                ]);
                return;
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al verificar NIT existente',
                'detalle' => $e->getMessage()
            ]);
            return;
        }

        try {
            $data = Clientes::find($id);
            
            if (!$data) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Cliente no encontrado'
                ]);
                return;
            }
            
            $data->sincronizar(
                [
                    'cliente_nombres' => $_POST['cliente_nombres'],
                    'cliente_apellidos' => $_POST['cliente_apellidos'],
                    'cliente_nit' => $_POST['cliente_nit'],
                    'cliente_telefono' => $_POST['cliente_telefono'],
                    'cliente_correo' => $_POST['cliente_correo'],
                    'cliente_situacion' => 1
                ]
            );
            $data->actualizar();
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La informacion del cliente ha sido modificada exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar cliente',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function eliminarCliente()
    {
        try {
            $id = filter_var($_POST['cliente_id'], FILTER_SANITIZE_NUMBER_INT);
            $consulta = "UPDATE clientes SET cliente_situacion = 0 WHERE cliente_id = $id";
            self::SQL($consulta);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Exito al eliminar'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar',
                'detalle' => $e->getMessage()
            ]);
        }
    }

}
