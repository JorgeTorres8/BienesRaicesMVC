<?php 
namespace Model;
/*Active Record sera la clase principal y sobre esta van a heredar las demas*/
class ActiveRecord {
        
    //Base DE DATOS
    protected static $db;
    protected static $columnaDB = [];
    protected static $tabla = '';

    //Errores
    protected static $errores = [];


    
    //Definir la conexion a la BD
    public static function setDB($database) {
        self::$db = $database;
    }

    public function guardar() {
        if(!is_null($this->id)) {
            //actualizar
            $this->actualizar();
        } else {
            //Creando un registro nuevo
            $this->crear();
        }
    }

    public function crear(){
        
        //Sanitizar los datos
        $atributos = $this->sanitizarAtributos();
        
        // Insertar en la base de datos       
        $query = " INSERT INTO " . static::$tabla .  " ( ";
        $query .= join(', ', array_keys($atributos)); //join creara un nuevo string a partir de un arreglo 
        $query .= " ) VALUES (' ";
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";
        $resultado = self::$db->query($query); 
        
        //Mensaje de exito o error
        if($resultado) {
            // Redireccionar al usuario.
            header('Location: /admin?resultado=1');
        }

    }

    public function actualizar() {
        //Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }
        $query = "UPDATE " . static::$tabla . " SET ";
        $query .= join(', ', $valores ); //join creara un nuevo string a partir de un arreglo 
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 ";
        $resultado = self::$db->query($query);
        
        if($resultado) {
            // Redireccionar al usuario.
            header('Location: /admin?resultado=2');
            }
    }

    public function eliminar() {
        //Eliminar el registro
        $query = "DELETE FROM " . static::$tabla . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $resultado = self::$db->query($query);

        if($resultado) {
            $this->borrarImagen();
            header('Location: /admin?resultado=3');
        }
    }

    //Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnaDB as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna; 
        }
        return $atributos;
    }
    
    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        
        foreach($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }

        return $sanitizado;
    }
    //Subida de archivos
    public function setImagen($imagen) {
        //Elimina la imagen previa

        if(!is_null($this->id)){
            $this->borrarImagen();
        }

        //Asignar el atributo de imagen el nombre de la imagen
        if($imagen) {
            $this->imagen = $imagen;
        }
    }
    //Elimina el archivo
    public function borrarImagen() {
        //comprobar si existe el archivo
        $existeArchivo = file_exists(CARPETA_IMAGENES . $this->imagen);
        if($existeArchivo) {
            unlink(CARPETA_IMAGENES . $this->imagen);
        }
    }
    //Validacion
    public static function getErrores() {
        return static::$errores;
    }

    public function validar() {
        static::$errores = [];
        return self::$errores;
    }

    //Lista todos los registros
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);

        return $resultado;
    }
    //Obtiene determinado numero de registros
    public static function get($cantidad) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT " . $cantidad; 
        $resultado = self::consultarSQL($query);

        return $resultado;
    }

    //Busca un registro por su ID
    public static function find($id) {
        $query = "SELECT * FROM ". static::$tabla . " WHERE id = ${id}";
        $resultado = self::consultarSQL($query);

        return array_shift($resultado); //funcion de php que nos va a retornar el primero elemento de un arreglo
    }

    public static function consultarSQL($query) {
        //Consultar la BD
        $resultado = self::$db->query($query);

        //Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        //Liberar memoria
        $resultado->free(); //Se realizara de todas formas, pero con estoo ayudamos al servidors

        return $array;

    }

    public static function crearObjeto($registro) {
        $objeto = new static; 

        foreach($registro as $key => $value) {
            if(property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }
        return $objeto;
    }
    //sincroniza el objeto en memoria con los cambios realizados por el usuario
    public function sincronizar( $args = [] ) {
        foreach($args as $key => $value) {
            if(property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }

}