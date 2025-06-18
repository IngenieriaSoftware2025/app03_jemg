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


        if (empty($_POST['marca_id']) || empty($_POST['modelo_nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Marca y nombre del modelo son campos obligatorios'
            ]);
            return;
        }


        $_POST['modelo_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['modelo_nombre']))));
        $_POST['modelo_descripcion'] = trim(htmlspecialchars($_POST['modelo_descripcion'] ?? ''));
        

        if (strlen($_POST['modelo_nombre']) < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del modelo debe tener más de 1 carácter'
            ]);
            return;
        }


        $_POST['modelo_precio_referencia'] = filter_var($_POST['modelo_precio_referencia'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
        if ($_POST['modelo_precio_referencia'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de referencia debe ser un valor positivo'
            ]);
            return;
        }


        try {
            $sql = "SELECT COUNT(*) as total FROM marcas 
                    WHERE marca_id = " . self::$db->quote($_POST['marca_id']) . "
                    AND marca_situacion = 1";
            
            $resultado = self::fetchFirst($sql);
            
            if (!$resultado || $resultado['total'] == 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La marca seleccionada no existe o no está activa'
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


        try {
            $sql = "SELECT COUNT(*) as total FROM modelos 
                    WHERE modelo_nombre = " . self::$db->quote($_POST['modelo_nombre']) . "
                    AND marca_id = " . self::$db->quote($_POST['marca_id']) . "
                    AND modelo_situacion = 1";
            
            $resultado = self::fetchFirst($sql);
            
            if ($resultado && $resultado['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un modelo con ese nombre para la marca seleccionada'
                ]);
                return;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al verificar modelo existente',
                'detalle' => $e->getMessage()
            ]);
            return;
        }


        try {
            $modelo = new Modelos([
                'marca_id' => $_POST['marca_id'],
                'modelo_nombre' => $_POST['modelo_nombre'],
                'modelo_descripcion' => $_POST['modelo_descripcion'],
                'modelo_precio_referencia' => $_POST['modelo_precio_referencia'],
                'modelo_situacion' => 1
            ]);

            $crear = $modelo->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Éxito, el modelo ' . $_POST['modelo_nombre'] . ' ha sido registrado correctamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar modelo',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function buscarModelo()
    {
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

            // Consulta SIN especificaciones
            $sql = "SELECT mod.modelo_id, mod.marca_id, mod.modelo_nombre, 
                        mod.modelo_descripcion, mod.modelo_precio_referencia, 
                        mod.modelo_situacion, mar.marca_nombre
                    FROM modelos mod
                    JOIN marcas mar ON mod.marca_id = mar.marca_id
                    WHERE $where
                    ORDER BY mar.marca_nombre, mod.modelo_nombre";
            
            $data = self::fetchArray($sql);

            ob_clean();
            
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Modelos obtenidos correctamente',
                'data' => $data
            ]);
            
        } catch (Exception $e) {
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
        getHeadersApi();

        $id = $_POST['modelo_id'];

        // Validaciones básicas de campos obligatorios
        if (empty($_POST['marca_id']) || empty($_POST['modelo_nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Marca y nombre del modelo son campos obligatorios'
            ]);
            return;
        }

        // Sanitización de datos (SIN especificaciones)
        $_POST['modelo_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['modelo_nombre']))));
        $_POST['modelo_descripcion'] = trim(htmlspecialchars($_POST['modelo_descripcion'] ?? ''));
        
        // Validación de nombre mínimo
        if (strlen($_POST['modelo_nombre']) < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del modelo debe tener más de 1 carácter'
            ]);
            return;
        }

        // Sanitización y validación de precio
        $_POST['modelo_precio_referencia'] = filter_var($_POST['modelo_precio_referencia'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
        if ($_POST['modelo_precio_referencia'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de referencia debe ser un valor positivo'
            ]);
            return;
        }

        // VALIDACIÓN: Verificar que la marca existe y está activa
        try {
            $sql = "SELECT COUNT(*) as total FROM marcas 
                    WHERE marca_id = " . self::$db->quote($_POST['marca_id']) . "
                    AND marca_situacion = 1";
            
            $resultado = self::fetchFirst($sql);
            
            if (!$resultado || $resultado['total'] == 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La marca seleccionada no existe o no está activa'
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

        // VALIDACIÓN: Verificar modelo único por marca (excluyendo el actual)
        try {
            $sql = "SELECT COUNT(*) as total FROM modelos 
                    WHERE modelo_nombre = " . self::$db->quote($_POST['modelo_nombre']) . "
                    AND marca_id = " . self::$db->quote($_POST['marca_id']) . "
                    AND modelo_id != " . self::$db->quote($id) . "
                    AND modelo_situacion = 1";
            
            $resultado = self::fetchFirst($sql);
            
            if ($resultado && $resultado['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe otro modelo con ese nombre para la marca seleccionada'
                ]);
                return;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al verificar modelo existente',
                'detalle' => $e->getMessage()
            ]);
            return;
        }

        try {
            $data = Modelos::find($id);
            
            if (!$data) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Modelo no encontrado'
                ]);
                return;
            }
            
            $data->sincronizar([
                'marca_id' => $_POST['marca_id'],
                'modelo_nombre' => $_POST['modelo_nombre'],
                'modelo_descripcion' => $_POST['modelo_descripcion'],
                'modelo_precio_referencia' => $_POST['modelo_precio_referencia'],
                'modelo_situacion' => 1
            ]);
            
            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El modelo ' . $_POST['modelo_nombre'] . ' ha sido modificado exitosamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
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



