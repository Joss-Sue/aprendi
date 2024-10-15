<?php
include "../models/multimediaModel.php";
session_start();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            //http://localhost/massivedemo/api/valoracionesController.php/?idProductoMultimedia=3
            $productosRespuesta = MultimediaClass::buscarAllProductos($_GET['idProductoMultimedia']);
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
            /*ejemplo json[{"tipoArchivo":"imagen","ruta":"//", "idProductoMulti":"1"},
            {"tipoArchivo":"video","ruta":"//", "idProductoMulti":"1"},{},..]*/

            $dataArray = json_decode(file_get_contents('php://input'), true);

            foreach($dataArray as $data) {

                extract($data);
                
                if(empty($tipoArchivo) || empty($ruta) || empty($idProductoMulti)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                }

                $resultadoFuncion = MultimediaClass::registrarProducto($tipoArchivo, $ruta, $idProductoMulti);

               if ($resultadoFuncion[0]==false){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => $resultadoFuncion[1]));
                exit;
               }
            }
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => $resultadoFuncion[1]));
               break;
            }
            
    case 'PUT':
        {
            /*ejemplo json [{"id":1,"ruta":"//"},{"id":2, "ruta":"//"},{},..]*/
            $dataArray = json_decode(file_get_contents('php://input'), true);
            foreach($dataArray as $data) {
                extract($data);
                
                if(empty($ruta)||empty($id)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                   exit;
                }

                $resultadoFuncion = MultimediaClass::editarProducto($id,$ruta);
                if ($resultadoFuncion[0]==false){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "error al actualizar un archivo" . $resultadoFuncion[1]));
                    exit;
                }
            }
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => "actualizado con exito"));
                break;
        }
        
    case 'DELETE':
        {
            /*ejemplo json[{"id":2},{"id":2}]*/
            $dataArray = json_decode(file_get_contents('php://input'), true);
            foreach($dataArray as $data) {
            $id = (isset($data['id']))?($data['id']):null;
            
            if(empty($id)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "id vacio"));
                exit;
            }

            $resultadoFuncion = MultimediaClass::eliminarProducto($id);
            if ($resultadoFuncion[0]==false){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "error al eliminar un archivo" . $resultadoFuncion[1]));
                exit;
            }
        }
            http_response_code(200);
            echo json_encode(array("status" => "success", "message" => "eliminado(s) con exito"));
            break;
        }
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}

?>