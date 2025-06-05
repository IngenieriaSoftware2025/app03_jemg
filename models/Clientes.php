<?php
// crea nombre de espacio Model
namespace Model;
// Importa la clase ActiveRecord del nombre de espacio Model
use Model\ActiveRecord;
// Crea la clase de instancia Clientes y hereda las funciones de ActiveRecord
class Clientes extends ActiveRecord {
    
    // Crea las propiedades de la clase
    public static $tabla = 'clientes';
    public static $idTabla = 'cliente_id';
    public static $columnasDB = 
    [
        'cliente_nombres',
        'cliente_apellidos',
        'cliente_nit',
        'cliente_telefono',
        'cliente_correo',
        'cliente_situacion'
    ];
    
    // Crea las variables para almacenar los datos
    public $cliente_id;
    public $cliente_nombres;
    public $cliente_apellidos;
    public $cliente_nit;
    public $cliente_telefono;
    public $cliente_correo;
    public $cliente_situacion;
    
    public function __construct($cliente = [])
    {
        $this->cliente_id = $cliente['cliente_id'] ?? null;
        $this->cliente_nombres = $cliente['cliente_nombres'] ?? '';
        $this->cliente_apellidos = $cliente['cliente_apellidos'] ?? '';
        $this->cliente_nit = $cliente['cliente_nit'] ?? 0;
        $this->cliente_telefono = $cliente['cliente_telefono'] ?? 0;
        $this->cliente_correo = $cliente['cliente_correo'] ?? '';
        $this->cliente_situacion = $cliente['cliente_situacion'] ?? 1;
    }

}