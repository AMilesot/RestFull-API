<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller; 

class Productos extends REST_Controller { 

    public function __construct(){

        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");

        
        parent::__construct();
        $this->load->database(); 
    }

    public function index_get($pagina = 0){ //El index es como el constructor en el servicio REST (hasta ahorita con mi entendimiento) y si solo queremos hacer alucion al nombre del servicio y que nos regrese algo, el index es por default que lo tiene que tener y es el metodo que se ejecuta al llamar solo al controlador

        $pagina = $pagina * 10;

        $query = $this->db->query("SELECT * FROM `productos` LIMIT ". $pagina .",10 "); 
        //$this->response($query->result());  metodo normal a como lo entendí, funciona al 100 pero no es como se va a manipular
        $respuesta = array('error' => FALSE , 'productos' => $query->result_array() );
        $this->response($respuesta);
    }

    public function productosPorTipo_get($tipo = 0, $pagina = 0){
        $pagina = $pagina * 10;
        $query = $this->db->query("SELECT * FROM `productos` WHERE linea_id = ". $tipo ." LIMIT ". $pagina .",10 ");
        $respuesta = array('error' => FALSE , 'productos' => $query->result_array() );
        $this->response($respuesta);
    }

    public function buscar_get($termino, $pagina = 0){
        $pagina = $pagina * 10;
        $query = $this->db->query("SELECT * FROM `productos` WHERE producto LIKE '%".$termino."%' LIMIT ". $pagina .",10 ");
        $respuesta = array('error' => FALSE , 'termino' => $termino, 'productos' => $query->result_array() );
        $this->response($respuesta);
    }

}