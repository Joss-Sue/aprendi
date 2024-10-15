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
            $resultadoFuncion = obtenerPedidos($idUsuario, $conexion);
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
    
    case 'POST':
        {
            $idCarrito = $_POST['idCarrito'];
            $idUsuario = $_POST['idUsuario'];
            
            $data = json_decode(file_get_contents('php://input'), true);
                
            if(empty($idCarrito) || empty($idUsuario)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                exit;
            }

            $conexion=BD::crearInstancia();
            
            $resultadoFuncion = registrarProducto($idCarrito,$idUsuario,$conexion);

            if ($resultadoFuncion[0]){
                http_response_code(200);
                $json_response = ["success" => true];
                echo json_encode($json_response);
                exit;
            }else{
                http_response_code(400);
                $json_response = ["error" => true];
                echo json_encode($json_response);
                exit;
            }

            break;
            }
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}

function registrarProducto($idCarrito,$idUsuario,$conexion){
    
    
    try{
        $sqlInsert="call crearPedido(:idCarrito,:idUsuario);";
        $consultaInsert= $conexion->prepare($sqlInsert);
        $consultaInsert->execute([
                                ':idCarrito'=>$idCarrito,
                                ':idUsuario'=>$idUsuario
        ]);
        

        return array(true,"insertado con exito");
    
    }catch(PDOException $e){
        
            return array(false, "Error al comprar: " . $e->getMessage());
    }
}

function obtenerPedidos($idUsuario, $conexion){
    
    
    try{
        $sqlInsert="select id, totalPedido, fechaPedido, estatusPedido from pedidos where idUsuarioPedido = :idUsuario order by fechaPedido desc";
        $consultaInsert= $conexion->prepare($sqlInsert);
        $consultaInsert ->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $consultaInsert->execute();
        
        $pedidos = $consultaInsert->fetchAll(PDO::FETCH_ASSOC);
        //var_dump($pedidos);
    
        if(!$pedidos) {
           return null;
        }else{
        
           
            foreach ($pedidos as $indice => $pedido) {
                $sqlQuery="SELECT ventas.articulosTotales, productos.nombreProd, productos.descripcionProd, productos.idProd, ventas.precio
                FROM ventas
                JOIN productos ON ventas.idProductoVenta = productos.idProd
                WHERE ventas.idPedido = :idPedido";
                 $consultaInsert= $conexion->prepare($sqlQuery);
                 $consultaInsert->execute(['idPedido'=> $pedido['id']]);
                 $pedidoConsulta=$consultaInsert->fetchAll(PDO::FETCH_ASSOC);
                $pedidos[$indice]['productos'] = $pedidoConsulta;
            }
            return $pedidos;

        }

        
    
    }catch(PDOException $e){
            return null;
    }
}


?>