<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Servicios;

//C:\docker\app03_jemg\views\servicios\index.php
class ServicioController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('servicios/index', []);
    }

    public static function guardarServicio()
    {
        getHeadersApi();

        //saniticacion de nombre y validaccion con capital
        $_POST['servicio_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['servicio_nombre']))));

        $cantidad_nombre = strlen($_POST['servicio_nombre']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del servicio debe de tener mas de 1 caracteres'
            ]);
            return;
        }

        // Validación de la descripción (opcional)
        if (!empty($_POST['servicio_descripcion']) && trim($_POST['servicio_descripcion']) !== '') {
            $_POST['servicio_descripcion'] = ucwords(strtolower(trim(htmlspecialchars($_POST['servicio_descripcion']))));
            $cantidad_descripcion = strlen($_POST['servicio_descripcion']);

            if ($cantidad_descripcion < 5) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La descripcion debe de contener al menos 5 caracteres'
                ]);
                return;
            }
        } else {
            $_POST['servicio_descripcion'] = null;
        }

        // Validación del precio
        $_POST['servicio_precio'] = filter_var($_POST['servicio_precio'], FILTER_VALIDATE_FLOAT);

        if ($_POST['servicio_precio'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser mayor a 0'
            ]);
            return;
        }

        // Validación del tiempo estimado (opcional)
        if (!empty($_POST['servicio_tiempo_estimado']) && trim($_POST['servicio_tiempo_estimado']) !== '') {
            $_POST['servicio_tiempo_estimado'] = filter_var($_POST['servicio_tiempo_estimado'], FILTER_VALIDATE_INT);

            if ($_POST['servicio_tiempo_estimado'] <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El tiempo estimado debe ser mayor a 0 horas'
                ]);
                return;
            }
        } else {
            $_POST['servicio_tiempo_estimado'] = null;
        }

        // Verificar si el nombre del servicio ya existe
        $servicioExistente = self::fetchFirst("SELECT servicio_id FROM servicios WHERE servicio_nombre = '{$_POST['servicio_nombre']}'");
        if ($servicioExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe un servicio con ese nombre'
            ]);
            return;
        }

        //se envian los datos a guardar despues de sanitizar
        try {
            $servicio = new Servicios(
                [
                    'servicio_nombre' => $_POST['servicio_nombre'],
                    'servicio_descripcion' => $_POST['servicio_descripcion'],
                    'servicio_precio' => $_POST['servicio_precio'],
                    'servicio_tiempo_estimado' => $_POST['servicio_tiempo_estimado'],
                    'servicio_situacion' => 1

                ]
            );

            $crear = $servicio->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Exito al guardar servicio'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar servicio',
                'detalle' => $e->getMessage()
            ]);
            return;
        }
    }

    public static function buscarServicio(){
        
        try {
            $sql = "SELECT * FROM servicios WHERE servicio_situacion = 1";
            $data = self::fetchArray($sql);

            if (count($data) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Servicios obtenidos correctamente',
                    'data' => $data

                ]);
            }else{
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al obtener servicios',
                    'detalle' => 'No hay servicios'
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

    public static function modificarServicio(){
        getHeadersApi();

        $id = $_POST['servicio_id'];

        $_POST['servicio_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['servicio_nombre']))));

        $cantidad_nombre = strlen($_POST['servicio_nombre']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del servicio debe de tener mas de 1 caracteres'
            ]);
            return;
        }

        // Validación de la descripción (opcional)
        if (!empty($_POST['servicio_descripcion']) && trim($_POST['servicio_descripcion']) !== '') {
            $_POST['servicio_descripcion'] = ucwords(strtolower(trim(htmlspecialchars($_POST['servicio_descripcion']))));
            $cantidad_descripcion = strlen($_POST['servicio_descripcion']);

            if ($cantidad_descripcion < 5) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La descripcion debe de contener al menos 5 caracteres'
                ]);
                return;
            }
        } else {
            $_POST['servicio_descripcion'] = null;
        }

        // Validación del precio
        $_POST['servicio_precio'] = filter_var($_POST['servicio_precio'], FILTER_VALIDATE_FLOAT);

        if ($_POST['servicio_precio'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser mayor a 0'
            ]);
            return;
        }

        // Validación del tiempo estimado (opcional)
        if (!empty($_POST['servicio_tiempo_estimado']) && trim($_POST['servicio_tiempo_estimado']) !== '') {
            $_POST['servicio_tiempo_estimado'] = filter_var($_POST['servicio_tiempo_estimado'], FILTER_VALIDATE_INT);

            if ($_POST['servicio_tiempo_estimado'] <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El tiempo estimado debe ser mayor a 0 horas'
                ]);
                return;
            }
        } else {
            $_POST['servicio_tiempo_estimado'] = null;
        }

        // Verificar si el nombre del servicio ya existe (excluyendo el servicio actual)
        $servicioExistente = self::fetchFirst("SELECT servicio_id FROM servicios WHERE servicio_nombre = '{$_POST['servicio_nombre']}' AND servicio_id != $id");
        if ($servicioExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otro servicio con ese nombre'
            ]);
            return;
        }

       try {
            $data = Servicios::find($id);
            $data-> sincronizar(
                [
                    'servicio_nombre' => $_POST['servicio_nombre'],
                    'servicio_descripcion' => $_POST['servicio_descripcion'],
                    'servicio_precio' => $_POST['servicio_precio'],
                    'servicio_tiempo_estimado' => $_POST['servicio_tiempo_estimado'],
                    'servicio_situacion' => 1
                ]
            );
            $data->actualizar();
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La informacion del servicio ha sido modificada exitosamente'
            ]);
       } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar',
                'detalle' => $e->getMessage(),
            ]);
       }

    }

    public static function eliminarServicio()
    {
        try {
            $id = filter_var($_POST['servicio_id'], FILTER_SANITIZE_NUMBER_INT);
            $consulta = "UPDATE servicios SET servicio_situacion = 0 WHERE servicio_id = $id";
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