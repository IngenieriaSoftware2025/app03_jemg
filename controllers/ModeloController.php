<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Modelos;
use Model\Marcas;
use MVC\Router;

class ModeloController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        // Obtener marcas de la base de datos
        $marcas = Marcas::all();

        // Renderizar la vista de modelos y enviar marcas
        $router->render('modelos/index', [
            'marcas' => $marcas
        ]);
    }

    public static function obtenerMarcas()
    {
        try {
            $sql = "SELECT marca_id, marca_nombre FROM marcas WHERE marca_situacion = 1 ORDER BY marca_nombre";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Marcas obtenidas correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener marcas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarModelo()
    {
        getHeadersApi();

        // Validaciones básicas
        if (empty($_POST['marca_id']) || empty($_POST['modelo_nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Marca y nombre del modelo son campos obligatorios'
            ]);
            return;
        }

        // Sanitizar datos
        $_POST['modelo_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['modelo_nombre']))));
        $_POST['modelo_descripcion'] = trim(htmlspecialchars($_POST['modelo_descripcion'] ?? ''));
        $_POST['modelo_especificaciones'] = trim(htmlspecialchars($_POST['modelo_especificaciones'] ?? ''));

        try {

            $data = new Modelos([
                'marca_id' => $_POST['marca_id'],
                'modelo_nombre' => $_POST['modelo_nombre'],
                'modelo_descripcion' => $_POST['modelo_descripcion'],
                'modelo_especificaciones' => $_POST['modelo_especificaciones'],
                'modelo_precio_referencia' => $_POST['modelo_precio_referencia'] ?? 0.00,
                'modelo_situacion' => 1
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Éxito, el modelo ' . $_POST['modelo_nombre'] . ' ha sido registrado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarModelo()
    {
        // Evita que se muestren errores HTML
        header('Content-Type: application/json');
        
        try {
            $marca_id = isset($_GET['marca_id']) ? $_GET['marca_id'] : null;
            $precio_min = isset($_GET['precio_min']) ? $_GET['precio_min'] : null;
            $precio_max = isset($_GET['precio_max']) ? $_GET['precio_max'] : null;            
            $condiciones = ["mod.modelo_situacion = 1", "mar.marca_situacion = 1"];

            if ($marca_id) {
                $condiciones[] = "mod.marca_id = " . intval($marca_id);
            }

            if ($precio_min) {
                $condiciones[] = "mod.modelo_precio_referencia >= " . floatval($precio_min);
            }

            if ($precio_max) {
                $condiciones[] = "mod.modelo_precio_referencia <= " . floatval($precio_max);
            }

            $where = implode(" AND ", $condiciones);

            $sql = "SELECT mod.modelo_id, mod.marca_id, mod.modelo_nombre, 
                        mod.modelo_descripcion, mod.modelo_especificaciones, 
                        mod.modelo_precio_referencia, mod.modelo_situacion,
                        mar.marca_nombre
                    FROM modelos mod
                    JOIN marcas mar ON mod.marca_id = mar.marca_id
                    WHERE $where
                    ORDER BY mar.marca_nombre, mod.modelo_nombre";
            
            $data = self::fetchArray($sql);

            // Limpia cualquier output previo
            ob_clean();
            
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Modelos obtenidos correctamente',
                'data' => $data
            ]);
            
        } catch (Exception $e) {
            // Limpia cualquier output previo
            ob_clean();
            
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los modelos',
                'detalle' => $e->getMessage()
            ]);
        }
        exit;
    }

    public static function modificarModelo()
    {
        try {
            $id = filter_var($_POST['modelo_id'], FILTER_SANITIZE_NUMBER_INT);
            
            // Validaciones básicas
            if (empty($_POST['marca_id']) || empty($_POST['modelo_nombre'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Marca y nombre del modelo son campos obligatorios'
                ]);
                return;
            }

            // Sanitizar datos
            $modelo_nombre = ucwords(strtolower(trim(htmlspecialchars($_POST['modelo_nombre']))));
            $modelo_descripcion = trim(htmlspecialchars($_POST['modelo_descripcion'] ?? ''));
            $modelo_especificaciones = trim(htmlspecialchars($_POST['modelo_especificaciones'] ?? ''));
            $precio = $_POST['modelo_precio_referencia'] ?? 0.00;
            
            // Actualizar directamente con SQL
            $sql = "UPDATE modelos SET 
                    marca_id = {$_POST['marca_id']},
                    modelo_nombre = '{$modelo_nombre}',
                    modelo_descripcion = '{$modelo_descripcion}',
                    modelo_especificaciones = '{$modelo_especificaciones}',
                    modelo_precio_referencia = {$precio},
                    modelo_situacion = 1
                    WHERE modelo_id = {$id}";
            
            self::SQL($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El modelo ' . $modelo_nombre . ' ha sido modificado exitosamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar modelo',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function EliminarModelo()
    {
        try {
            $id = filter_var($_POST['modelo_id'], FILTER_SANITIZE_NUMBER_INT);
            $consulta = "UPDATE modelos SET modelo_situacion = 0 WHERE modelo_id = $id";
            self::SQL($consulta);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Éxito al eliminar modelo'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar modelo',
                'detalle' => $e->getMessage()
            ]);
        }
    }



}



