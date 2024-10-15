<?php
include "../models/cotizacionesModel.php";


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            
        }
        break;
    case 'POST':
        {
            $idCotizacion = $_POST['idCotizacion'];
            $estatus = $_POST['estatus'];
            
            //http://localhost/gitMassive/massive-php/api/modificarCotizaciones.php/ {"idCotizacion":"2","estatus":"1"}
            $data = json_decode(file_get_contents('php://input'), true);
            
            if(empty($idCotizacion) || empty($estatus)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                exit;
            }

            $resultadoFuncion = CotizacionClass::estatusCotizacion($idCotizacion, $estatus);

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