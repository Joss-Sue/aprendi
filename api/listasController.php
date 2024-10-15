<?php
include "../models/listasModel.php";
//session_start();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            
            //http://localhost/massivedemo/api/productosController.php/?idCarrito=1
            
            $productosRespuesta = ListasClass::buscarAllProductos($_GET['idUsuario']);
                if($productosRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ninguna lista encontrada"));
                    exit;
                }else{
                    http_response_code(200);
                    echo json_encode($productosRespuesta);
                    exit;
                }
            
            
        break;
        }
    case 'POST':
        {
            //ejemplo json{"idUsuario":12, "nombre":"lista test", "descripcion":"descripcion test"}

            $data = json_decode(file_get_contents('php://input'), true);

                extract($data);
               
                
                if(empty($idUsuario) || empty($nombre) || empty($descripcion) ){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                    exit;
                }

                $resultadoFuncion = ListasClass::registrarProducto($idUsuario, $nombre,$descripcion);

               if ($resultadoFuncion[0]){
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => $resultadoFuncion[1]));
                exit;
               }else{
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => $resultadoFuncion[1]));
                exit;
                }  
               break;
            }
            
    case 'PUT':
        {
            //ejemplo json {"id":1, "nombre":"lista test edit", "descripcion":"descripcion test edit"}
            $data = json_decode(file_get_contents('php://input'), true);
                extract($data);
                
                if(empty($id) || empty($nombre) || empty($descripcion)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                   exit;
                }

                $resultadoFuncion = ListasClass::editarProducto($id,$nombre,$descripcion);
                if ($resultadoFuncion[0]==false){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "error al actualizar un producto del carrito" . $resultadoFuncion[1]));
                    exit;
                }else{
                    http_response_code(200);
                echo json_encode(array("status" => "success", "message" => "actualizado con exito"));
                exit;
                }

                break;
        }
        
    case 'DELETE':
        {
            /*ejemplo json{"id":2}*/
            $data = json_decode(file_get_contents('php://input'), true);
            $stock = $_POST['id'];
            
            if(empty($id)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "id vacio"));
                exit;
            }

            $resultadoFuncion = ListasClass::eliminarProducto($data['id']);
            if ($resultadoFuncion[0]){
                    http_response_code(200);
                    $json_response = ["success" => true];
                    echo json_encode($json_response);
               }else{
                    http_response_code(400);
                    $json_response = ["error" => true];
                    echo json_encode($json_response);
                }
            break;
        }
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}

?>