<?php 

namespace Model;

class Vendedor extends ActiveRecord {
    protected static $tabla = 'vendedores';
    protected static $columnaDB = ['id', 'nombre', 'apellido', 'telefono'];

    public $id;
    public $nombre;
    public $apellido;
    public $telefono;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? NULL;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
    }

    public function validar() { 
        if(!$this->nombre) {
            self::$errores[] = "El Nombre es obligatorio";
        }
        if(!$this->apellido) {
            self::$errores[] = "El Apellido es obligatorio";
        }
        if(!$this->telefono) {
            self::$errores[] = "El Telefono es obligatorio";
        }
        if(!preg_match('/[0-9]{10}/', $this->telefono)){ //preg_match es una expresion regular, es como una forma de buscar un patrón dentro de un texto
            self::$errores[] = "Formato no Valido";
        }
        return self::$errores;
}   }