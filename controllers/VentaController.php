<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Ventas;
use Model\Clientes;
use Model\Inventario;

class VentaController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        try {
            // Obtener clientes activos para el select
            $clientes = Clientes::where('cliente_situacion', 1);
            
            $router->render('ventas/index', [
                'clientes' => $clientes
            ]);
        } catch (Exception $e) {
            $router->render('ventas/index', [
                'clientes' => []
            ]);
        }
    }

    public static function obtenerClientes()
    {
        try {
            $sql = "SELECT cliente_id, cliente_nombres, cliente_apellidos, cliente_nit,
                           (cliente_nombres || ' ' || cliente_apellidos) as cliente_completo
                    FROM clientes 
                    WHERE cliente_situacion = 1 
                    ORDER BY cliente_nombres, cliente_apellidos";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener clientes',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerInventario()
    {
        try {
            $sql = "SELECT i.inventario_id, i.modelo_id, i.inventario_stock_actual, 
                           i.inventario_precio_venta, m.modelo_nombre, ma.marca_nombre,
                           (ma.marca_nombre || ' - ' || m.modelo_nombre) as producto_completo
                    FROM inventario i
                    JOIN modelos m ON i.modelo_id = m.modelo_id
                    JOIN marcas ma ON m.marca_id = ma.marca_id
                    WHERE i.inventario_situacion = 1 AND i.inventario_stock_actual > 0
                    ORDER BY ma.marca_nombre, m.modelo_nombre";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Inventario obtenido correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener inventario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function procesarVenta()
    {
        getHeadersApi();

        // Validaciones básicas
        if (empty($_POST['cliente_id']) || empty($_POST['productos'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Cliente y productos son obligatorios'
            ]);
            return;
        }

        // Decodificar productos del carrito
        $productos = json_decode($_POST['productos'], true);
        if (empty($productos)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe agregar al menos un producto a la venta'
            ]);
            return;
        }

        // Validar total
        $total_calculado = 0;
        foreach ($productos as $producto) {
            $total_calculado += $producto['precio'] * $producto['cantidad'];
        }

        $total_recibido = floatval($_POST['venta_total']);
        if (abs($total_calculado - $total_recibido) > 0.01) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El total de la venta no coincide con los productos'
            ]);
            return;
        }

        // VALIDACIÓN 1: Verificar que el cliente existe y está activo
        try {
            $sql = "SELECT COUNT(*) as total FROM clientes 
                    WHERE cliente_id = " . self::$db->quote($_POST['cliente_id']) . "
                    AND cliente_situacion = 1";
            
            $resultado = self::fetchFirst($sql);
            
            if (!$resultado || $resultado['total'] == 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El cliente seleccionado no existe o no está activo'
                ]);
                return;
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al verificar cliente',
                'detalle' => $e->getMessage()
            ]);
            return;
        }

        // VALIDACIÓN 2: Verificar stock disponible para todos los productos
        foreach ($productos as $producto) {
            try {
                $sql = "SELECT inventario_stock_actual FROM inventario 
                        WHERE inventario_id = " . self::$db->quote($producto['inventario_id']) . "
                        AND inventario_situacion = 1";
                
                $stock = self::fetchFirst($sql);
                
                if (!$stock || $stock['inventario_stock_actual'] < $producto['cantidad']) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Stock insuficiente para el producto: ' . $producto['producto']
                    ]);
                    return;
                }
                
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al verificar stock',
                    'detalle' => $e->getMessage()
                ]);
                return;
            }
        }

        // TRANSACCIÓN: Procesar venta y actualizar inventario
        try {
            // Crear registro de venta
            $venta = new Ventas([
                'cliente_id' => $_POST['cliente_id'],
                'usuario_id' => 1, // Usuario temporal
                'venta_fecha' => date('Y-m-d H:i:s'),
                'venta_total' => $total_calculado,
                'venta_tipo' => 'VENTA',
                'venta_situacion' => 1
            ]);

            $resultado_venta = $venta->crear();

            if (!$resultado_venta['resultado']) {
                throw new Exception('Error al crear la venta');
            }

            $venta_id = $resultado_venta['id'];

            // Actualizar stock para cada producto vendido
            foreach ($productos as $producto) {
                $sql_update = "UPDATE inventario 
                              SET inventario_stock_actual = inventario_stock_actual - " . intval($producto['cantidad']) . ",
                                  inventario_fecha_actualizacion = '" . date('Y-m-d H:i:s') . "'
                              WHERE inventario_id = " . self::$db->quote($producto['inventario_id']);
                
                self::SQL($sql_update);
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta procesada exitosamente',
                'venta_id' => $venta_id,
                'total' => $total_calculado
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al procesar la venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function buscarVentas()
    {
        try {
            $sql = "SELECT v.venta_id, v.venta_fecha, v.venta_total, v.venta_tipo,
                           c.cliente_nombres, c.cliente_apellidos,
                           (c.cliente_nombres || ' ' || c.cliente_apellidos) as cliente_completo
                    FROM ventas v
                    JOIN clientes c ON v.cliente_id = c.cliente_id
                    WHERE v.venta_situacion = 1
                    ORDER BY v.venta_fecha DESC";
            $data = self::fetchArray($sql);

            if (count($data) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Ventas obtenidas correctamente',
                    'data' => $data
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay ventas registradas'
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

    public static function anularVenta()
    {
        getHeadersApi();

        $id = $_POST['venta_id'];

        try {
            // Obtener información de la venta antes de anular
            $venta = self::fetchFirst("SELECT * FROM ventas WHERE venta_id = " . self::$db->quote($id) . " AND venta_situacion = 1");
            
            if (!$venta) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Venta no encontrada'
                ]);
                return;
            }

            // Anular venta (eliminación lógica)
            $sql = "UPDATE ventas SET venta_situacion = 0 WHERE venta_id = " . self::$db->quote($id);
            self::SQL($sql);
            
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta anulada exitosamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al anular venta',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}