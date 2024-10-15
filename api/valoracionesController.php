<?php
include "../models/valoracionesModel.php";
session_start();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            //http://localhost/massivedemo/api/valoracionesController.php/?idProdVal=3
            $productosRespuesta = ValoracionesClass::buscarAllProductos($_GET['idProdVal']);
                if($productosRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ninguna valoracion encontrada"));
                }else{
                    http_response_code(200);
                    echo json_encode($productosRespuesta);
                }
        }
        break;
    case 'POST':
        {
            /*ejemplo json{"comentario":"que onda","valoracion":4,"idProdVal":3,"idUsuarioVal":2}*/

            $data = json_decode(file_get_contents('php://input'), true);

                extract($data);
                
                if(empty($comentario) || empty($valoracion) || empty($idProdVal)||empty($idUsuarioVal)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                }

                $resultadoFuncion = ValoracionesClass::registrarProducto($comentario,$valoracion, $idProdVal,$idUsuarioVal);

               if ($resultadoFuncion[0]){
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => $resultadoFuncion[1]));
               }else{
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => $resultadoFuncion[1]));
                }  
               break;
            }
            
    case 'PUT':
        {
            /*ejemplo json{"comentario":"que onda","valoracion":4,"id":4}*/
            $data = json_decode(file_get_contents('php://input'), true);

                extract($data);
                
                if(empty($comentario)||empty($id)||empty($valoracion)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                   exit;
                }

                $resultadoFuncion = ValoracionesClass::editarProducto($id ,$comentario, $valoracion);
                if ($resultadoFuncion[0]==false){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "error al actualizar la valoracion" . $resultadoFuncion[1]));
                    exit;
                }
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => "actualizado con exito"));
                break;
        }
        
    case 'DELETE':
        {
            /*ejemplo json{"id":2}*/
            $data = json_decode(file_get_contents('php://input'), true);
            $id = (isset($data['id']))?($data['id']):null;
            
            if(empty($id)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "id vacio"));
                exit;
            }

            $resultadoFuncion = ValoracionesClass::eliminarProducto($data['id']);
            if ($resultadoFuncion[0]){
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => $resultadoFuncion[1]));
               }else{
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => $resultadoFuncion[1]));
                }
            break;
        }
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}

?>