<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\REST_Controller; 

class Pedidos extends REST_Controller { 

    public function __construct(){

        header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header("Access-Control-Allow-Origin: *");

        
        parent::__construct();
        $this->load->database(); 
    }

    public function crearOrden_post ($token = "0", $idUser = "0"){
        $data = $this->post();

        if($token == "0" || $idUser == "0") 
        {
            $respuesta = array('error' => TRUE , 'mensaje' => 'Faltan datos', 'token' => $token, 'id' => $idUser );
            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        if(!isset($data['items']) || strlen($data['items']) == 0)
        {
            $respuesta = array('error' => TRUE , 'mensaje' => 'No se ha cargado ningun articulo' );
            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);
            return;
        }

        //validar token y id
        $condiciones = array('id' => $idUser, 'token' => $token );
        $query =  $this->db->get_where('login',$condiciones);
        /*En el curso se explica como...
          $this->db->where($condiciones);
          $query = $this->db->get('login'); */
          $existe = $query->row();
          if(!$existe)
          {
            $respuesta = array('error' => TRUE , 'mensaje' => 'Usuario y Token incorrecto' );
            $this->response($respuesta,REST_Controller::HTTP_UNAUTHORIZED);
            return;
          }

          //Usuario y Token correctos
          $this->db->reset_query();

          $insertar = array('usuario_id' => $idUser ); // se crea el arreglo con el registro a insertar

          $this->db->insert('ordenes', $insertar); // se inserta el campo en la BG pasando como referencia en primera instancia la tabla y posteriormente un arreglo con el registro a insertar
          $idOrden = $this->db->insert_id(); //regresa el ultimo id insertado en la tabla, si y solo si sea autoincrementable

          // Generar el detalle de la orden
          $this->db->reset_query();

          $items = explode(',', $data['items']); // regresa un arreglo (es como el trim en c#) cada que encuentre una coma "," generará un nuevo campo en el arreglo a regresar

          foreach ($items as &$idProducto) // el ampersar se usa como apuntador (no se nada mas, ni porque ni como ni cuando jajaaj)
          {
             $insertarDatos = array('producto_id' => $idProducto, 'orden_id' => $idOrden); // si se quiere hacer mas pro hay que validar que el $idProducto realmente exista o sea un producto valido de la BD
             $this->db->insert('ordenes_detalle', $insertarDatos);
          }

          $respuesta = array('error' => FALSE, 'OrdenId' => $idOrden );
          $this->response($respuesta);
    }

    public function obtenerOrdenes_get($token = "0", $idUser = "0")
    {
        if($token == "0" || $idUser == "0") 
        {
            $respuesta = array('error' => TRUE , 'mensaje' => 'Faltan datos' );
            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $condiciones = array('id' => $idUser, 'token' => $token );
        $query =  $this->db->get_where('login',$condiciones);
        
        $existe = $query->row();
        if(!$existe)
        {
            $respuesta = array('error' => TRUE , 'mensaje' => 'Usuario y Token incorrecto' );
            $this->response($respuesta,REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        //Obtener todas las ordenes del usuario
        $this->db->reset_query();
        $condicion = array('usuario_id' => $idUser );
        $query = $this->db->get_where('ordenes', $condicion);

        $ordenes = array();
        foreach ($query->result() as $row) 
        {
            $queryDetalle = $this->db->query('SELECT OD.orden_id, P.* FROM ordenes_detalle OD INNER JOIN productos P ON OD.producto_id = P.codigo WHERE orden_id = '. $row->id); //en PHP se concatena con un punto.
            $orden = array('id' => $row->id , 'creado_en' => $row->creado_en, 'detalle' => $queryDetalle->result() );
            array_push( $ordenes, $orden ); // instruccion para agregar dentro de otro array un registro de otro array, poniendo en primera instancia el array principal y segunda el array a insertar en el primero
        }

        $respuesta = array('error' => FALSE, 'Ordenes' => $ordenes );
        $this->response($respuesta);

    }

    public function borrarOrden_delete($token = "0", $idUser = "0", $idOrden = "0" )
    {
        if($token == "0" || $idUser == "0" || $idOrden == "0") 
        {
            $respuesta = array('error' => TRUE , 'mensaje' => 'Faltan datos' );
            $this->response($respuesta,REST_Controller::HTTP_BAD_REQUEST);
            return;
        }
        $condiciones = array('id' => $idUser, 'token' => $token );
        $query =  $this->db->get_where('login',$condiciones);
        
        $existe = $query->row();
        if(!$existe)
        {
            $respuesta = array('error' => TRUE , 'mensaje' => 'Usuario y Token incorrecto' );
            $this->response($respuesta,REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        //Verificar que la orden sea del uruario (algo que a mi parecer no tiene logica porque obvio solo te mostrará la losta de Ordenes del usurario. so...)
        $this->db->reset_query();
        $condiciones = array('id' => $idOrden, 'usuario_id' => $idUser );
        $query = $this->db->get_where('ordenes', $condiciones);

        

        if (!$query->row()) // es lo mismo que crear una variable existe, solo que se ahorra ese paso
        {
            $respuesta = array('error' => TRUE , 'mensaje' => 'Orden incaccesible, verifique la información','anexo' => $condiciones );
            $this->response($respuesta,REST_Controller::HTTP_UNAUTHORIZED);
            return;
        }

        //Todo bien ya se puede Eliminar la orden, se ha validado toda la informacion
        $condicion = array('id' => $idOrden );
        $this->db->delete('ordenes', $condicion);

        $condicion = array('orden_id' => $idOrden );
        $this->db->delete('ordenes_detalle', $condicion);

        $respuesta = array('error' => FALSE, 'mensaje' => 'Orden #'.$idOrden .' borrada con exito' );

        $this->response($respuesta);
    }
    

}