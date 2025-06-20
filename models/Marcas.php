<?php
// crea nombre de espacio Model
namespace Model;
// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;
// Crea la clase de instancia Marcas y hereda las funciones de ActiveRecord
class Marcas extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'marcas';
    public static $idTabla = 'marca_id';
    public static $columnasDB = 
    [
        'marca_nombre',
        'marca_descripcion',
        'marca_situacion'
    ];
    
    // Crea las variables para almacenar los datos
    public $marca_id;
    public $marca_nombre;
    public $marca_descripcion;
    public $marca_situacion;
    
    public function __construct($marca = [])
    {
        $this->marca_id = $marca['marca_id'] ?? null;
        $this->marca_nombre = $marca['marca_nombre'] ?? '';
        $this->marca_descripcion = $marca['marca_descripcion'] ?? '';
        $this->marca_situacion = $marca['marca_situacion'] ?? 1;
    }

}