<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller; 

class Lineas extends REST_Controller { 

    public function __construct(){

        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");

        
        parent::__construct();
        $this->load->database(); 
    }

    public function index_get(){ //El index es como el constructor en el servicio REST (hasta ahorita con mi entendimiento) y si solo queremos hacer alucion al nombre del servicio y que nos regrese algo, el index es por default que lo tiene que tener y es el metodo que se ejecuta al llamar solo al controlador

        $query = $this->db->query("SELECT * FROM `lineas`"); 
        //$this->response($query->result());  metodo normal a como lo entendí, funciona al 100 pero no es como se va a manipular
        $respuesta = array('error' => FALSE , 'lineas' => $query->result() );
        $this->response($respuesta);
    }

}