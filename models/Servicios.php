<?php
// crea nombre de espacio Model
namespace Model;
// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;
// Crea la clase de instancia Servicios y hereda las funciones de ActiveRecord
class Servicios extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'servicios';
    public static $idTabla = 'servicio_id';
    public static $columnasDB = 
    [
        'servicio_nombre',
        'servicio_descripcion',
        'servicio_precio',
        'servicio_tiempo_estimado',
        'servicio_situacion'
    ];
    
    // Crea las variables para almacenar los datos
    public $servicio_id;
    public $servicio_nombre;
    public $servicio_descripcion;
    public $servicio_precio;
    public $servicio_tiempo_estimado;
    public $servicio_situacion;
    
    public function __construct($servicio = [])
    {
        $this->servicio_id = $servicio['servicio_id'] ?? null;
        $this->servicio_nombre = $servicio['servicio_nombre'] ?? '';
        $this->servicio_descripcion = $servicio['servicio_descripcion'] ?? '';
        $this->servicio_precio = $servicio['servicio_precio'] ?? 0.00;
        $this->servicio_tiempo_estimado = $servicio['servicio_tiempo_estimado'] ?? null;
        $this->servicio_situacion = $servicio['servicio_situacion'] ?? 1;
    }

}