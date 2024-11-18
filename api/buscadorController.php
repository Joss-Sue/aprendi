<?php
include "../models/cursosModel.php";


switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        {
             //http://localhost/massivedemo/api/buscardorController.php/?id=3&pagina=1
            // si palabra_buscar='', si categori_id=0, si instructor_id=0 se omite el filtro

            $data = json_decode(file_get_contents('php://input'), true);

            extract($data);
            //echo $palabra_buscar; echo $categoria_id; echo $instructor_id;
            if( !isset($palabra_buscar) || !isset($categoria_id) || !isset($instructor_id) ){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                exit;
            }
            $resultadoFuncion = null;
            if(empty($fecha_inicio) ){
                $resultadoFuncion = CursoClass::buscarCursos ($palabra_buscar, $categoria_id, $instructor_id, "1999-01-01", "2099-12-12");
            }else{
            $resultadoFuncion = CursoClass::buscarCursos ($palabra_buscar, $categoria_id, $instructor_id, $fecha_inicio, $fecha_fin);
            }
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