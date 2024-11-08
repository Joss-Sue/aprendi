<?php
include "../models/valoracionesModel.php";
session_start();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            //http://localhost/aprendi/api/valoracionesController.php/?id_curso=3&id_estudiante=2
            if (isset($_GET['id_curso']) && isset($_GET['id_estudiante'])) {
                $productosRespuesta = ValoracionesClass::buscarProductoByID($_GET['id_curso'], $_GET['id_estudiante']);
                if($productosRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ninguna valoracion encontrada"));
                }else{
                    http_response_code(200);
                    echo json_encode($productosRespuesta);
                }exit;
            }else{
                //http://localhost/aprendi/api/valoracionesController.php/?id=1
                if (isset($_GET['id'])){
                    $productosRespuesta = ValoracionesClass::buscarAllProductos($_GET['id']);
                    if($productosRespuesta==null){
                        http_response_code(400);
                        echo json_encode(array("status" => "error", "message" => "ninguna valoracion encontrada"));
                    }else{
                        http_response_code(200);
                        echo json_encode($productosRespuesta);
                    } exit;
                }
            }
        }
        break;
    case 'POST':
        {
            /*ejemplo json{"contenido":"que onda","calificacion":4,"curso_id":3,"usuario_id":2}*/

            $data = json_decode(file_get_contents('php://input'), true);

                extract($data);
                
                if(empty($contenido) || empty($calificacion) || empty($curso_id)||empty($usuario_id)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                }

                $resultadoFuncion = ValoracionesClass::registrarProducto($contenido,$calificacion,$curso_id,$usuario_id);

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
            /*ejemplo json{"contenido":"que onda","calificacion":4,"id":4}*/
            $data = json_decode(file_get_contents('php://input'), true);

                extract($data);
                
                if(empty($contenido)||empty($id)||empty($calificacion)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                   exit;
                }

                $resultadoFuncion = ValoracionesClass::editarProducto($id ,$contenido, $calificacion);
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