<?php

require_once '../config/bd_conexion.php';


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            $conexion=BD::crearInstancia();
            $data = json_decode(file_get_contents('php://input'), true);

            
                
            if(!isset($_GET['idUsuario'])){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                exit;
            }
            $idUsuario= intval($_GET['idUsuario']);
            $resultadoFuncion = obtenerVentas($idUsuario, $conexion);
            if ($resultadoFuncion==null){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "error"));
                exit;
             
            }else{
                http_response_code(200);
                echo json_encode($resultadoFuncion);
                exit;
             }
        

            break;
        }
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}


function obtenerVentas($idUsuario, $conexion){
    
    
    try{
        $sqlInsert="select ventas.articulosTotales, productos.nombreProd, productos.stockProd, ventas.precio, ventas.fechaVenta, ventas.precio * ventas.articulosTotales as total 
        from ventas inner join productos on ventas.idProductoVenta = productos.idProd where productos.vendedorProd = :idUsuario;";
        $consultaInsert= $conexion->prepare($sqlInsert);
        $consultaInsert ->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $consultaInsert->execute();
        
        $pedidos = $consultaInsert->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($pedidos);
    
        if(!$pedidos) {
           return null;
        }else{
        
            return $pedidos;

        }

        
    
    }catch(PDOException $e){
            return null;
    }
}


?>