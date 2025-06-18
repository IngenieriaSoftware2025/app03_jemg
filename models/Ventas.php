<?php
// crea nombre de espacio Model
namespace Model;
// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;
// Crea la clase de instancia Ventas y hereda las funciones de ActiveRecord
class Ventas extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'ventas';
    public static $idTabla = 'venta_id';
    public static $columnasDB = 
    [
        'cliente_id',
        'usuario_id',
        'venta_fecha',
        'venta_total',
        'venta_tipo',
        'venta_situacion'
    ];
    
    // Crea las variables para almacenar los datos
    public $venta_id;
    public $cliente_id;
    public $usuario_id;
    public $venta_fecha;
    public $venta_total;
    public $venta_tipo;
    public $venta_situacion;
    
    public function __construct($venta = [])
    {
        $this->venta_id = $venta['venta_id'] ?? null;
        $this->cliente_id = $venta['cliente_id'] ?? null;
        $this->usuario_id = $venta['usuario_id'] ?? 1; 
        $this->venta_fecha = $venta['venta_fecha'] ?? null;
        $this->venta_total = $venta['venta_total'] ?? 0.00;
        $this->venta_tipo = $venta['venta_tipo'] ?? 'VENTA';
        $this->venta_situacion = $venta['venta_situacion'] ?? 1;
    }

}