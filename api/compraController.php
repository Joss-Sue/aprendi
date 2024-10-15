<?php
//include "../models/valoracionesModel.php";
require_once '../config/bd_conexion.php';
session_start();


switch ($_SERVER['REQUEST_METHOD']) {
    
    case 'POST':
        {
            /*ejemplo json[{"articulosTotales":"3", "idProducto":"3"},{"articulosTotales":"5", "idProducto":"1"}]*/
            if(isset($_SERVER['HTTP_ID']) && isset($_SERVER['HTTP_COSTO'])){
                $dataArray = json_decode(file_get_contents('php://input'), true);

                //extract($data);
                
                //if(empty($comentario) || empty($valoracion) || empty($idProdVal)||empty($idUsuarioVal)){
                    //http_response_code(400);
                    //echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                //}
                $conexion=BD::crearInstancia();
                $total = floatval($_SERVER['HTTP_COSTO']); $idUsuario = intval($_SERVER['HTTP_ID']);
                var_dump($total); var_dump($idUsuario);
                
                $resultadoFuncion = registrarProducto($total,$idUsuario, $conexion, $dataArray);

               if ($resultadoFuncion[0]){
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => $resultadoFuncion[1]));
               }else{
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => $resultadoFuncion[1]));
                }
            }else{
            http_response_code(400);
            echo json_encode(array("status" => "error headers", "message" => "faltan headers"));
            }
               break;
            }
         
        
    
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}

function registrarProducto($total,$idUsuario, $conexion, $dataArray){
    
    
    try{
        $sqlInsert="call crearPedido(:total,:idUsuario,@lastId);";
        $consultaInsert= $conexion->prepare($sqlInsert);
        $consultaInsert->execute([
                                "total" => $total,
                                "idUsuario" => $idUsuario
                                ]);
        
        $sql="select @lastID";
        $sentencia = $conexion-> prepare($sql);
        $sentencia -> execute();
    
        $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
        //echo $producto["@lastID"];
        
        foreach($dataArray as $data){
            extract($data);
            echo $producto["@lastID"];
            var_dump($articulosTotales); var_dump($idProducto);
            $sqlInsert2="call insertarVenta(:articulos, :idPedido, :idProducto);";
            $consultaInsert2= $conexion->prepare($sqlInsert2);
            $consultaInsert2->execute([
                                ":articulos"=>$articulosTotales,
                                ":idPedido"=>$producto["@lastID"],
                                ":idProducto"=>$idProducto
                                ]);
        }

        return array(true,"insertado con exito");
    
    }catch(PDOException $e){
        if ($e->errorInfo[1] == 1062) {
            $cadena = "El comentario esta repetido.";
            return array(false, $cadena);
        } else {
            return array(false, "Error al agregar comentario: " . $e->getMessage());
        }
    }
}

?>