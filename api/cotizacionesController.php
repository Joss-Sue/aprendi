<?php
include "../models/cotizacionesModel.php";


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            if (isset($_GET['clientId'])){
                //http://localhost/massivedemo/api/productosController.php/?clientId=1
                $productosRespuesta = CotizacionClass::getClienteCotizaciones($_GET['clientId']);
                if($productosRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun usuario encontrado"));
                    exit;
                }else{
                    http_response_code(200);
                    echo json_encode($productosRespuesta);
                }exit; 
            }elseif (isset($_GET['vendedorId'])){
                //http://localhost/massivedemo/api/productosController.php/?vendedorId=1
                $productosRespuesta = CotizacionClass::getVendedorCotizaciones($_GET['vendedorId']);
                if($productosRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun usuario encontrado"));
                    exit;
                }else{
                    http_response_code(200);
                    echo json_encode($productosRespuesta);
                }exit;
            }
        }
        break;
    case 'POST':
        {

            $idProducto = $_POST['idProducto']; 
            $idCliente = $_POST['idCliente']; 
            $idVendedor = $_POST['idVendedor']; 
            $precioReal = $_POST['precioReal']; 
            $precioSolicitado = $_POST['precioSolicitado']; 
            $Estatus = $_POST['Estatus'];

            $data = json_decode(file_get_contents('php://input'), true);
            
            if(empty($idProducto) || empty($idCliente) || empty($idVendedor) || empty($precioReal) || empty($precioSolicitado) || empty($Estatus)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
            }

            $resultadoFuncion = CotizacionClass::registrarCotizacion($idProducto, $idCliente, $idVendedor, $precioReal, $precioSolicitado, $Estatus);

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
        }
    case 'DELETE':
        {
        }
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}

?>