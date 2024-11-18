<?php
include "../models/cursosModel.php";


switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        {
           
            $data = json_decode(file_get_contents('php://input'), true);

            extract($data);
           
            if( !isset($filtro) ){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                exit;
            }
            
            $resultadoFuncion = CursoClass::buscarCursosPantallaPrincipal($filtro);
                if($resultadoFuncion==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun curso con ese instructor encontrado"));
                }else{
                    http_response_code(200);
                    echo json_encode($resultadoFuncion);
                }exit; 
                    
            http_response_code(400);
            echo json_encode(array("status" => "error", "message" => "ningun curso encontrado"));
        }
        break;
    
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}

?>