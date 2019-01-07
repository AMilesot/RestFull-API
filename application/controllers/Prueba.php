<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php'); //linea para reconocer la ruta del controlador REST
use Restserver\libraries\REST_Controller; // complemento de linea de arriba

class Prueba extends REST_Controller { //se cambia CI_Controller por el resrController ya que este ultimo extiende a CI_controller en su definicion

    public function __construct(){

        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *"); // las conjuncion de las 3 lineas de codigo nos permite recibir peticiones desde cualquier origen y de los metodos definifinidos en la primer linea

        
        parent::__construct();// siempre que usa el constructor hay que poner esta linea ( no se por que exactamente), para que ejecute el constructor padre
        $this->load->database(); //siempre ponerla debajo de la linea de patent si no, marcará error
    }

    public function index(){

        echo "Hola Mundo";   
    }

    public function dameArreglo(){
        $arreglo = array("Manzana", "Pera", "Uva");

        echo json_encode($arreglo);
    }

    public function dameArregloParametro_get($index = 0){ //se tiene que agregar guin bajo (_) y el tipo de metodo que se requiera (GET, POST, OPTION, PUT, DELETE, PATCH) para que sea compatible con el servicio REST

        $arreglo = array("Manzana", "Pera", "Uva");

        if($index >= sizeof( $arreglo ))
        {
            $respuesta = array('error'=> TRUE, 'mensaje:' => "Valor fuera del rango"); //para igualar alguna valor dentro del arreglo se usa "=>" para asignarlo
            $this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);// Se agrega la palabra reservada REST_Controller para definir el status de la peticion siempre con "::" para acceder a la constante
        }
            
        else
        {
            //echo json_encode($arreglo[$index]); --- Se cambia linea de codigo por compatibilidad con servicio REST
            $respuesta = array('error' => FALSE , 'Item(fruta)' => $arreglo[$index]);
            //$this->response($arreglo[$index]); se cambia por el arreglo respuesta en igual de directamente la posicion del arreglo
            $this->response($respuesta);
        }
    }

    public function obtenerProdOld($codigo){

        //$this->load->database(); se pasa al constructor
        $query = $this->db->query("SELECT * FROM `productos` where codigo = '".$codigo."'"); //para poder poner la varia se necesita poner dentro de los apostrofes comillas normales puntos y dentro el normbre de la variable

        //$query->result() 
       

        echo json_encode( $query->result() );
    }

    public function obtenerProd_get($codigo){ //misma funcion de arriba pero ahora como un verdadero servicio REST

        //$this->load->database(); se comenta esta linea de codigo porque se va a ejecutar en el constructor para no ponerla en cada metodo que se vaya a ocupar
        $query = $this->db->query("SELECT * FROM `productos` where codigo = '".$codigo."'"); //para poder poner la varia se necesita poner dentro de los apostrofes comillas normales puntos y dentro el normbre de la variable

        //$query->result() 

        //echo json_encode( $query->result() ); igual que en el ejemplo anterior se cambia el echo por la respuesta al get
        $this->response($query->result());
    }
}