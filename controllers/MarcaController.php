<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Marcas;

//C:\docker\app03_jemg\views\marcas\index.php
class MarcaController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('marcas/index', []);
    }

    public static function guardarMarca()
    {
        getHeadersApi();

        // Sanitización de nombre de marca y validación con capital
        $_POST['marca_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['marca_nombre']))));

        $cantidad_nombre = strlen($_POST['marca_nombre']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre de la marca debe de tener mas de 1 caracteres'
            ]);
            return;
        }

        // Sanitización de descripción (opcional)
        $_POST['marca_descripcion'] = trim(htmlspecialchars($_POST['marca_descripcion'] ?? ''));

        // Validar que el nombre de la marca no exista ya (para evitar duplicados)
        try {
            $marcaExistente = Marcas::where('marca_nombre', $_POST['marca_nombre']);
            if (!empty($marcaExistente)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe una marca con ese nombre'
                ]);
                return;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al verificar marca existente',
                'detalle' => $e->getMessage()
            ]);
            return;
        }

        // Se envían los datos a guardar después de sanitizar
        try {
            $marca = new Marcas([
                'marca_nombre' => $_POST['marca_nombre'],
                'marca_descripcion' => $_POST['marca_descripcion'],
                'marca_situacion' => 1
            ]);

            $crear = $marca->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Éxito al guardar marca'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar marca',
                'detalle' => $e->getMessage()
            ]);
            return;
        }
    }

    public static function buscarMarca()
    {
        
        try {
            $sql = "SELECT * FROM marcas WHERE marca_situacion = 1";
            $data = self::fetchArray($sql);

            if (count($data) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Marcas obtenidas correctamente',
                    'data' => $data
                ]);
            }else{
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al obtener marcas',
                    'detalle' => 'No hay marcas registradas'
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

    public static function modificarMarca()
    {
        getHeadersApi();

        $id = $_POST['marca_id'];

        // Sanitización de nombre de marca y validación con capital
        $_POST['marca_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['marca_nombre']))));

        $cantidad_nombre = strlen($_POST['marca_nombre']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre de la marca debe de tener mas de 1 caracteres'
            ]);
            return;
        }

        // Sanitización de descripción (opcional)
        $_POST['marca_descripcion'] = trim(htmlspecialchars($_POST['marca_descripcion'] ?? ''));

        // Validar que el nombre de la marca no exista ya en otra marca (evitar duplicados)
        try {
            $marcaExistente = Marcas::where('marca_nombre', $_POST['marca_nombre']);
            if (!empty($marcaExistente)) {
                // Verificar que no sea la misma marca que se está editando
                foreach ($marcaExistente as $marca) {
                    if ($marca->marca_id != $id) {
                        http_response_code(400);
                        echo json_encode([
                            'codigo' => 0,
                            'mensaje' => 'Ya existe otra marca con ese nombre'
                        ]);
                        return;
                    }
                }
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al verificar marca existente',
                'detalle' => $e->getMessage()
            ]);
            return;
        }

        try {
            $data = Marcas::find($id);
            $data->sincronizar([
                'marca_nombre' => $_POST['marca_nombre'],
                'marca_descripcion' => $_POST['marca_descripcion'],
                'marca_situacion' => 1
            ]);
            $data->actualizar();
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información de la marca ha sido modificada exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar marca',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function eliminarMarca()
    {
        try {
            $id = filter_var($_POST['marca_id'], FILTER_SANITIZE_NUMBER_INT);
            $consulta = "UPDATE marcas SET marca_situacion = 0 WHERE marca_id = $id";
            self::SQL($consulta);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Éxito al eliminar marca'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar marca',
                'detalle' => $e->getMessage()
            ]);
        }
    }

}