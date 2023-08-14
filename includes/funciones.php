<?php

define('TEMPLATES_URL', __DIR__ . '/templates');
define('FUNCIONES_URL', __DIR__ . 'funciones.php');
define('CARPETA_IMAGENES', $_SERVER['DOCUMENT_ROOT'] . '/imagenes/');  //__DIR__ . '/../imagenes/') Video 399
/*        //Crear carpeta
        $carpetaImagenes = '../../imagenes/';

        if(!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes);
        } */

function incluirTemplate( string  $nombre, bool $inicio = false ) {
    include TEMPLATES_URL . "/${nombre}.php"; 
}

function estaAutenticado() {
    session_start();

    if(!$_SESSION['login']) {
        header('Location: /' );
    }

}

function debugear($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function ValidarTipoContenido($tipo) {
    $tipos = ['vendedor', 'propiedad'];

    return in_array($tipo, $tipos);  //Nos permitira buscar un string dentro de un arreglo o un valor dentro de un arreglo 
}

function mostrarNotificaciones($codigo) {
    $mensaje = '';

    switch($codigo) {
        case 1 :
            $mensaje = 'Creado Crrectamente';
            break;
        case 2 :
            $mensaje = 'Actualizado Crrectamente';
            break;
        case 3 :
            $mensaje = 'Eliminado Crrectamente';
            break;
        default:
            $mensaje = false;
            break;     
    }
    return $mensaje;
}

function validarORedireccionar(string $url) {
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if(!$id) {
        header("Location: ${url}");
    }

    return $id;
}