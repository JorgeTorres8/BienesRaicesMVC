<?php 

namespace Controllers;

use MVC\Router;
use Model\Propiedad;
use PHPMailer\PHPMailer\PHPMailer;

class PaginasController {
    public static function index(Router $router){

        $propiedades = Propiedad::get(3);
        $inicio = true;

        $router->render('paginas/index', [
            'propiedades' => $propiedades,
            'inicio' => $inicio
        ]);
    }

    public static function nosotros(Router $router){
        $router->render('paginas/nosotros');
    }

    public static function propiedades(Router $router){

        $propiedades = Propiedad::all();

        $router->render('paginas/propiedades', [
            'propiedades' => $propiedades
        ]);
    }

    public static function propiedad( Router $router){
        $id = validarORedireccionar('/propiedades');

        $propiedad = Propiedad::find($id);

        $router->render('paginas/propiedad', [
            'propiedad'=> $propiedad
        ]);
    }

    public static function blog(Router $router){
        $router->render('paginas/blog');
    }

    public static function entrada(Router $router){
        $router->render('paginas/entrada');
    }

    public static function contacto(Router $router){
        $mensaje = null;  
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $mail = new PHPMailer();
            $respuestas = $_POST['contacto'];

            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = '10a5d028e7a1a4';
            $mail->Password = '00736b998a7e3a';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 2525;

            $mail->setFrom('admin@bienesraices.com'); 
            $mail->addAddress('admin@bienesraices.com' , 'BienesRaices.com');
            $mail->Subject = 'Tienes un nuevo Mensaje';

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8'; 

            $contenido = '<html>' ;
            $contenido .= '<p>Tienes un nuevo mensaje </p>';
            $contenido .= '<p>Nombre: ' . $respuestas['nombre']     . '</p>';

            if($respuestas['contacto'] === 'telefono'){
                $contenido .= '<p>Eligio ser contactado por Teléfono</p>';
                $contenido .= '<p>Telefono: ' . $respuestas['telefono']     . '</p>';
                $contenido .= '<p>Fecha contacto: ' . $respuestas['fecha']     . '</p>';
                $contenido .= '<p>Hora: ' . $respuestas['hora']     . '</p>';
            } else {
                $contenido .= '<p>Eligio ser contactado por e-mail </p>';
                $contenido .= '<p>Email: ' . $respuestas['email']     . '</p>';
            }

            $contenido .= '<p>Mensaje: ' . $respuestas['mensaje']     . '</p>';
            $contenido .= '<p>Vende o Compra: ' . $respuestas['tipo']     . '</p>';
            $contenido .= '<p>Precio o Presupuesto: $' . $respuestas['precio']     . '</p>';
            $contenido .= '<p>Prefiere ser contactado por: ' . $respuestas['contacto']     . '</p>';
            $contenido .= '</html>';

            $mail->Body = $contenido;
            $mail->AltBody = 'Esto es texto alternativo sin HTML';

            if($mail->send()) {
                $mensaje = "Mensaje Enviado Correctamente";
            } else {
                $mensaje = "No se pudo enviar";
            }
        }

        $router->render('/paginas/contacto', [
            'mensaje' => $mensaje
        ]);
    }

}