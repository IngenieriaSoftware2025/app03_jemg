<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Inventario;
use Model\Modelos;
use Model\Marcas;

class InventarioController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        try {
            // Obtener modelos con sus marcas para el select
            $sql = "SELECT m.modelo_id, m.modelo_nombre, ma.marca_nombre
                    FROM modelos m 
                    JOIN marcas ma ON m.marca_id = ma.marca_id 
                    WHERE m.modelo_situacion = 1 AND ma.marca_situacion = 1 
                    ORDER BY ma.marca_nombre, m.modelo_nombre";
            
            $modelos = self::fetchArray($sql);
            
            // Convertir a objetos para usar en la vista
            $modelosObjetos = [];
            foreach($modelos as $modelo) {
                $modelosObjetos[] = (object) $modelo;
            }
            
            $router->render('inventario/index', [
                'modelos' => $modelosObjetos
            ]);
        } catch (Exception $e) {
            $router->render('inventario/index', [
                'modelos' => []
            ]);
        }
    }

    public static function obtenerModelos()
    {
        try {
            // CORRECCIÓN INFORMIX: Usar operador || en lugar de CONCAT()
            $sql = "SELECT m.modelo_id, m.modelo_nombre, ma.marca_nombre,
                        (ma.marca_nombre || ' - ' || m.modelo_nombre) as modelo_completo
                    FROM modelos m 
                    JOIN marcas ma ON m.marca_id = ma.marca_id 
                    WHERE m.modelo_situacion = 1 AND ma.marca_situacion = 1 
                    ORDER BY ma.marca_nombre, m.modelo_nombre";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Modelos obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener modelos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarInventario()
    {
        getHeadersApi();

        // Validaciones básicas de campos obligatorios
        if (empty($_POST['modelo_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un modelo'
            ]);
            return;
        }

        // Sanitización y validación de stock
        $_POST['inventario_stock_actual'] = filter_var($_POST['inventario_stock_actual'], FILTER_VALIDATE_INT);

        if ($_POST['inventario_stock_actual'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El stock no puede ser negativo'
            ]);
            return;
        }

        // Sanitización y validación de precio de venta
        $_POST['inventario_precio_venta'] = filter_var($_POST['inventario_precio_venta'], FILTER_VALIDATE_FLOAT);

        if ($_POST['inventario_precio_venta'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de venta debe ser mayor a 0'
            ]);
            return;
        }

        // Sanitización y validación de precio de compra (opcional)
        if (!empty($_POST['inventario_precio_compra']) && trim($_POST['inventario_precio_compra']) !== '') {
            $_POST['inventario_precio_compra'] = filter_var($_POST['inventario_precio_compra'], FILTER_VALIDATE_FLOAT);

            if ($_POST['inventario_precio_compra'] <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El precio de compra debe ser mayor a 0'
                ]);
                return;
            }

            // VALIDACIÓN DE NEGOCIO: Margen mínimo de ganancia
            if ($_POST['inventario_precio_venta'] <= $_POST['inventario_precio_compra']) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El precio de venta debe ser mayor al precio de compra'
                ]);
                return;
            }
            } else {
                $_POST['inventario_precio_compra'] = null;
            }

        // VALIDACIÓN 1: Verificar que el modelo existe y está activo
        try {
            $sql = "SELECT COUNT(*) as total FROM modelos 
                    WHERE modelo_id = " . self::$db->quote($_POST['modelo_id']) . "
                    AND modelo_situacion = 1";
            
            $resultado = self::fetchFirst($sql);
            
            if (!$resultado || $resultado['total'] == 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El modelo seleccionado no existe o no está activo'
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

        // VALIDACIÓN 2: Verificar inventario único por modelo (patrón consolidado)
        try {
            $sql = "SELECT COUNT(*) as total FROM inventario 
                    WHERE modelo_id = " . self::$db->quote($_POST['modelo_id']) . "
                    AND inventario_situacion = 1";
            
            $resultado = self::fetchFirst($sql);
            
            if ($resultado && $resultado['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Este modelo ya está registrado en el inventario'
                ]);
                return;
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al verificar inventario existente',
                'detalle' => $e->getMessage()
            ]);
            return;
        }

        // Creación del inventario
        try {
            $inventario = new Inventario([
                'modelo_id' => $_POST['modelo_id'],
                'inventario_stock_actual' => $_POST['inventario_stock_actual'],
                'inventario_precio_venta' => $_POST['inventario_precio_venta'],
                'inventario_precio_compra' => $_POST['inventario_precio_compra'],
                'inventario_fecha_actualizacion' => date('Y-m-d H:i:s'),
                'inventario_situacion' => 1
            ]);

            $crear = $inventario->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Éxito al registrar producto en inventario'
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar en inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarInventario()
    {
        try {
            // CORRECCIÓN INFORMIX: Usar operador || en lugar de CONCAT()
            $sql = "SELECT i.inventario_id, i.modelo_id, i.inventario_stock_actual, 
                        i.inventario_precio_venta, i.inventario_precio_compra, 
                        i.inventario_fecha_actualizacion,
                        m.modelo_nombre, ma.marca_nombre,
                        (ma.marca_nombre || ' - ' || m.modelo_nombre) as modelo_completo
                    FROM inventario i
                    JOIN modelos m ON i.modelo_id = m.modelo_id
                    JOIN marcas ma ON m.marca_id = ma.marca_id
                    WHERE i.inventario_situacion = 1
                    ORDER BY ma.marca_nombre, m.modelo_nombre";
            $data = self::fetchArray($sql);

            if (count($data) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Inventario obtenido correctamente',
                    'data' => $data
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al obtener inventario',
                    'detalle' => 'No hay productos en inventario'
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

    public static function modificarInventario()
    {
        getHeadersApi();

        $id = $_POST['inventario_id'];

        // Sanitización y validación de stock
        $_POST['inventario_stock_actual'] = filter_var($_POST['inventario_stock_actual'], FILTER_VALIDATE_INT);

        if ($_POST['inventario_stock_actual'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El stock no puede ser negativo'
            ]);
            return;
        }

        // Sanitización y validación de precio de venta
        $_POST['inventario_precio_venta'] = filter_var($_POST['inventario_precio_venta'], FILTER_VALIDATE_FLOAT);

        if ($_POST['inventario_precio_venta'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de venta debe ser mayor a 0'
            ]);
            return;
        }

        // Sanitización y validación de precio de compra (opcional)
        if (!empty($_POST['inventario_precio_compra']) && trim($_POST['inventario_precio_compra']) !== '') {
            $_POST['inventario_precio_compra'] = filter_var($_POST['inventario_precio_compra'], FILTER_VALIDATE_FLOAT);

            if ($_POST['inventario_precio_compra'] <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El precio de compra debe ser mayor a 0'
                ]);
                return;
            }

            
            if ($_POST['inventario_precio_venta'] <= $_POST['inventario_precio_compra']) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El precio de venta debe ser mayor al precio de compra'
                ]);
                return;
            }
        } else {
            $_POST['inventario_precio_compra'] = null;
        }

        
        try {
            $data = Inventario::find($id);
            
            if (!$data) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Registro de inventario no encontrado'
                ]);
                return;
            }
            
            $data->sincronizar([
                'inventario_stock_actual' => $_POST['inventario_stock_actual'],
                'inventario_precio_venta' => $_POST['inventario_precio_venta'],
                'inventario_precio_compra' => $_POST['inventario_precio_compra'],
                'inventario_fecha_actualizacion' => date('Y-m-d H:i:s'),
                'inventario_situacion' => 1
            ]);
            
            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del inventario ha sido modificada exitosamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarInventario()
    {
        try {
            $id = filter_var($_POST['inventario_id'], FILTER_SANITIZE_NUMBER_INT);
            $consulta = "UPDATE inventario SET inventario_situacion = 0 WHERE inventario_id = $id";
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