<?php
// crea nombre de espacio Model
namespace Model;
// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;
// Crea la clase de instancia Inventario y hereda las funciones de ActiveRecord
class Inventario extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'inventario';
    public static $idTabla = 'inventario_id';
    public static $columnasDB = 
    [
        'modelo_id',
        'inventario_stock_actual',
        'inventario_precio_venta',
        'inventario_precio_compra',
        'inventario_fecha_actualizacion',
        'inventario_situacion'
    ];
    
    // Crea las variables para almacenar los datos
    public $inventario_id;
    public $modelo_id;
    public $inventario_stock_actual;
    public $inventario_precio_venta;
    public $inventario_precio_compra;
    public $inventario_fecha_actualizacion;
    public $inventario_situacion;
    
    public function __construct($inventario = [])
    {
        $this->inventario_id = $inventario['inventario_id'] ?? null;
        $this->modelo_id = $inventario['modelo_id'] ?? null;
        $this->inventario_stock_actual = $inventario['inventario_stock_actual'] ?? 0;
        $this->inventario_precio_venta = $inventario['inventario_precio_venta'] ?? 0.00;
        $this->inventario_precio_compra = $inventario['inventario_precio_compra'] ?? null;
        $this->inventario_fecha_actualizacion = $inventario['inventario_fecha_actualizacion'] ?? null;
        $this->inventario_situacion = $inventario['inventario_situacion'] ?? 1;
    }

}