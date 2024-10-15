<?php
//include "../models/valoracionesModel.php";
require_once '../config/bd_conexion.php';
session_start();


switch ($_SERVER['REQUEST_METHOD']) {
    
    case 'GET':
        {       //siempre mandar id, si es funcion que no lo necesita mandar 0
                if (isset($_GET['tipo']) && isset($_GET['id'])) {

                     $conexion=BD::crearInstancia();
                    $contarRespuesta=contarFilas($_GET['tipo'],$conexion,$_GET['id']);
                    if($contarRespuesta==null){
                        http_response_code(400);
                        echo json_encode(array("status" => "error", "message" => "ningun usuario encontrado"));
                        break;
                    }else{
                        http_response_code(200);
                        echo json_encode($contarRespuesta);  
                        break;
                    }       
                }
        }
    
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}


function contarFilas($tipo,$conexion,$id){
        
    if($id==0){
    if($tipo=="productosByAll"){
        $sentenciaIf="select count(*) as filas from productos where activoProd = 1";
    }elseif ($tipo=="productosByAllAdmin") {
        $sentenciaIf="select count(*) as filas from productos where activoProd = 1 and estaListadoProd = 0";
    }
    var_dump($sentenciaIf) ;
    $sentencia = $conexion-> prepare($sentenciaIf);
    $sentencia -> execute([]);

    $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
    }else{
        if($tipo=="productosByVendedor"){
            $sentenciaIf="select count(*) as filas from productos where vendedorProd = :id";
    }elseif($tipo=="productosByAdmin"){
            $sentenciaIf="select count(*) as filas from productos where activoProd = 1 and adminAutoriza = :id";
    }
    $sentencia = $conexion-> prepare($sentenciaIf);
    $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
    $sentencia -> execute();
    $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    if(!$producto) {
       return null;
    }else{
        return $producto;
    }
}


?>