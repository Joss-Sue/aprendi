<?php
include "../models/categoriasModel.php";
session_start();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            //http://localhost/massivedemo/api/categoriaController.php/?tipoCategoria=subcategoria
            if (isset($_GET['id'])) {
                $categoriaRespuesta = CategoriaClass::buscarCategoriaByID($_GET['id']);
                if($categoriaRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun usuario encontrado"));
                    exit;
                }else{
                    http_response_code(200);
                    echo json_encode($categoriaRespuesta);
                }
            }else{
                $categoriaRespuesta = CategoriaClass::buscarAllCategorias();
                if($categoriaRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ninguna categoria encontrada"));
                    exit;
                }else{
                    http_response_code(200);
                    echo json_encode($categoriaRespuesta);
                }
            }
        }
        break;
    case 'POST':
        {
            /*ejemplo json{"nombre":"Audifonos","descripcion":"audifonos de todo tipo","createdBy":2}*/
            $data = json_decode(file_get_contents('php://input'), true);

            extract($data);

                if(empty($nombre) || empty($descripcion) || empty($createdBy)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                    exit;
                }

                $resultadoFuncion = CategoriaClass::registrarCategoria($nombre, $descripcion, $createdBy);

                if ($resultadoFuncion[0]){
                        http_response_code(200);
                        $json_response = ["success" => true];
                        echo json_encode($json_response);
                }else{
                        http_response_code(400);
                        $json_response = ["error" => true];
                        echo json_encode($json_response);
                }
                exit;
            }
            
    case 'PUT':
        {
            /*ejemplo json{"nombre":"Cargadores Laptop","descripcion":"cargadores de laptops","createdBy":4,"id":2}*/
            $data = json_decode(file_get_contents('php://input'), true);

            extract($data);
            
            if(empty($id) || empty($nombre) || empty($descripcion) || empty($createdBy)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                exit;
            }

            $resultadoFuncion = CategoriaClass::editarCategoria($id, $nombre, $descripcion, $createdBy);
            if ($resultadoFuncion[0]){
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => $resultadoFuncion[1]));
               }else{
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => $resultadoFuncion[1]));
            }
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

            $resultadoFuncion = CategoriaClass::eliminarCategoria($data['id']);
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