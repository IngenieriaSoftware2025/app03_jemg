<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Inventario;
use Model\Modelos;
use Model\Marcas;

//C:\docker\app03_jemg\views\inventario\index.php
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
            $sql = "SELECT m.modelo_id, m.modelo_nombre, ma.marca_nombre,
                           CONCAT(ma.marca_nombre, ' - ', m.modelo_nombre) as modelo_completo
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

        // Validaciones básicas
        if (empty($_POST['modelo_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un modelo'
            ]);
            return;
        }

        // Validación del stock
        $_POST['inventario_stock_actual'] = filter_var($_POST['inventario_stock_actual'], FILTER_VALIDATE_INT);

        if ($_POST['inventario_stock_actual'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El stock no puede ser negativo'
            ]);
            return;
        }

        // Validación del precio de venta
        $_POST['inventario_precio_venta'] = filter_var($_POST['inventario_precio_venta'], FILTER_VALIDATE_FLOAT);

        if ($_POST['inventario_precio_venta'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de venta debe ser mayor a 0'
            ]);
            return;
        }

        // Validación del precio de compra (opcional)
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
        } else {
            $_POST['inventario_precio_compra'] = null;
        }

        // Verificar que el modelo no esté ya en inventario
        $inventarioExistente = self::fetchFirst("SELECT inventario_id FROM inventario WHERE modelo_id = {$_POST['modelo_id']} AND inventario_situacion = 1");
        if ($inventarioExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Este modelo ya está registrado en el inventario'
            ]);
            return;
        }

        //se envian los datos a guardar despues de sanitizar
        try {
            $inventario = new Inventario(
                [
                    'modelo_id' => $_POST['modelo_id'],
                    'inventario_stock_actual' => $_POST['inventario_stock_actual'],
                    'inventario_precio_venta' => $_POST['inventario_precio_venta'],
                    'inventario_precio_compra' => $_POST['inventario_precio_compra'],
                    'inventario_fecha_actualizacion' => date('Y-m-d H:i:s'),
                    'inventario_situacion' => 1

                ]
            );

            $crear = $inventario->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Exito al guardar en inventario'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar en inventario',
                'detalle' => $e->getMessage()
            ]);
            return;
        }
    }

    public static function buscarInventario(){
        
        try {
            $sql = "SELECT i.inventario_id, i.modelo_id, i.inventario_stock_actual, 
                           i.inventario_precio_venta, i.inventario_precio_compra, 
                           i.inventario_fecha_actualizacion,
                           m.modelo_nombre, ma.marca_nombre,
                           CONCAT(ma.marca_nombre, ' - ', m.modelo_nombre) as modelo_completo
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
            }else{
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

    public static function modificarInventario(){
        getHeadersApi();

        $id = $_POST['inventario_id'];

        // Validación del stock
        $_POST['inventario_stock_actual'] = filter_var($_POST['inventario_stock_actual'], FILTER_VALIDATE_INT);

        if ($_POST['inventario_stock_actual'] < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El stock no puede ser negativo'
            ]);
            return;
        }

        // Validación del precio de venta
        $_POST['inventario_precio_venta'] = filter_var($_POST['inventario_precio_venta'], FILTER_VALIDATE_FLOAT);

        if ($_POST['inventario_precio_venta'] <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio de venta debe ser mayor a 0'
            ]);
            return;
        }

        // Validación del precio de compra (opcional)
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
        } else {
            $_POST['inventario_precio_compra'] = null;
        }

       try {
            $data = Inventario::find($id);
            $data-> sincronizar(
                [
                    'inventario_stock_actual' => $_POST['inventario_stock_actual'],
                    'inventario_precio_venta' => $_POST['inventario_precio_venta'],
                    'inventario_precio_compra' => $_POST['inventario_precio_compra'],
                    'inventario_fecha_actualizacion' => date('Y-m-d H:i:s'),
                    'inventario_situacion' => 1
                ]
            );
            $data->actualizar();
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La informacion del inventario ha sido modificada exitosamente'
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