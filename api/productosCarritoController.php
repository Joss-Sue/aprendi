<?php
include "../models/productosCarritoModel.php";
session_start();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            //http://localhost/massivedemo/api/productosController.php/?idCarrito=1
            $productosRespuesta = ProductosCarritoClass::buscarAllProductos($_GET['idCarrito']);
                if($productosRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun producto en el carrito encontrado"));
                }else{
                    http_response_code(200);
                    echo json_encode($productosRespuesta);
                }
        }
        break;
    case 'POST':
        {
            /*ejemplo json{"idCarrito":5, "cantidad":1, "productoID":2, "precioCarrito":163.70}*/

            $idCarrito = $_POST['idCarrito'];
            $cantidad = $_POST['cantidad'];
            $productoID = $_POST['productoID'];
            $precioCarrito = $_POST['precioCarrito'];

            $data = json_decode(file_get_contents('php://input'), true);
            
            if(empty($idCarrito) || empty($cantidad) || empty($productoID) || empty($precioCarrito)){
                http_response_code(400);
                $json_response = ["error" => true];
                echo json_encode($json_response);
            }

            $resultadoFuncion = ProductosCarritoClass::registrarProducto($idCarrito,$cantidad,$productoID, $precioCarrito);

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
            /*ejemplo json [{"id":1,"cantidad":2},{"id":2, "cantidad":1},{},..]*/
            $dataArray = json_decode(file_get_contents('php://input'), true);
            foreach($dataArray as $data) {
                extract($data);
                
                if(empty($cantidad)||empty($id)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                   exit;
                }

                $resultadoFuncion = ProductosCarritoClass::editarProducto($id,$cantidad);
                if ($resultadoFuncion[0]==false){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "error al actualizar un producto del carrito" . $resultadoFuncion[1]));
                    exit;
                }
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

            $resultadoFuncion = ProductosCarritoClass::eliminarProducto($data['id']);
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