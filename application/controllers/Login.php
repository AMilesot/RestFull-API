<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller; 

class Login extends REST_Controller { 

    public function __construct(){

        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");

        
        parent::__construct();
        $this->load->database(); 
    }

    public function index_post(){ //El index es como el constructor en el servicio REST (hasta ahorita con mi entendimiento) y si solo queremos hacer alucion al nombre del servicio y que nos regrese algo, el index es por default que lo tiene que tener y es el metodo que se ejecuta al llamar solo al controlador

        $data = $this->post();//forma de obtener la data con el metodo post
        if(!isset($data['correo']) || !isset($data['contrasena'])) //isset es para preguntar si existe el campo especificado
        {
            $respuesta = array('error' => TRUE , 'mensaje' => 'Faltan datos' );
            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);
            return; //es como el break en c#
        }
 
        //si se ingresaron datos en los recuadros

        $condiciones  = array('correo' => $data['correo'] , 'contrasena' => $data['contrasena'] ); // se igualan los datos desde la BD a las posiciones del arreglo

        $query = $this->db->get_where('login',$condiciones); // es como hacer un select where y compara al mismo tiempo con las condiciones de un array dado, especificando en primera instancia la tabla y despues un arreglo con que comparar los datos.

        $usuario = $query->row(); // forma de asignar el valor del registro extraido de la BD a una variable

        if(!isset($usuario))
        {
            $respuesta = array('error' => TRUE , 'mensaje' => 'Datos no encontrados Usuario y/o contraseña invalida' );
            $this->response($respuesta,REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        //En este punto ya tenemos validado que si estan todos los datos y ademas son validos, por lo cual se generará un TOKEN! a partir del correo electronico

        //TOKEN!!!//

        //$token = bin2hex(openssl_random_pseudo_bytes(20)); // forma de crear un token aleatoriamente, generando un numero hexadecimal al hazar de 20 caracteres
        $token = hash('ripemd160',$data['correo']); //genera un hash del dato especificado, en este caso el correo

        //Guardar token en la BD

        $this->db->reset_query(); // limpia la funcion (creo que es funcion) query, pero de que limpia el query lo hace para preparar otra peticion del tipo query
        $actualizarToken = array('token' => $token );
        $this->db->where('id', $usuario->id); // funcion para obtener el registro a modificar (select * from login where id = $usuario['id'])

        $todoBien = $this->db->update('login',$actualizarToken); // forma de insertar un campo  o un registro (depende del array) a un registro de la bd, especificacndo en primera instancia la tabla y despues los datos a modificar

        $respuesta = array('error' => FALSE , 'token' => $token, 'idUser' => $usuario->id );

        $this->response($respuesta);

        
    }
}