<?php
// crea nombre de espacio Model
namespace Model;
// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;
// Crea la clase de instancia Modelos y hereda las funciones de ActiveRecord
class Modelos extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'modelos';
    public static $idTabla = 'modelo_id';
    public static $columnasDB = 
    [
        'marca_id',
        'modelo_nombre',
        'modelo_descripcion',
        'modelo_especificaciones',
        'modelo_precio_referencia',
        'modelo_situacion'
    ];
    
    // Crea las variables para almacenar los datos
    public $modelo_id;
    public $marca_id;
    public $modelo_nombre;
    public $modelo_descripcion;
    public $modelo_especificaciones;
    public $modelo_precio_referencia;
    public $modelo_situacion;
    
    public function __construct($modelo = [])
    {
        $this->modelo_id = $modelo['modelo_id'] ?? null;
        $this->marca_id = $modelo['marca_id'] ?? null;
        $this->modelo_nombre = $modelo['modelo_nombre'] ?? '';
        $this->modelo_descripcion = $modelo['modelo_descripcion'] ?? '';
        $this->modelo_especificaciones = $modelo['modelo_especificaciones'] ?? '';
        $this->modelo_precio_referencia = $modelo['modelo_precio_referencia'] ?? 0.00;
        $this->modelo_situacion = $modelo['modelo_situacion'] ?? 1;
    }

}