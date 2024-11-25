<?php
include '../models/cursosModel.php';
switch ($_SERVER['REQUEST_METHOD']) {

        case 'POST':
            {
                // {"curso_id": 1, "nivel_id": 2, "estudiante_id": 6, "progreso": 50}
                $data = json_decode(file_get_contents('php://input'), true);
                
                extract($data);
            
                if (empty($id)) {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
                    exit;
                }
            
                $resultadoNivel = CursoClass::ultimoCursoInstructor($id);
                if (!$resultadoNivel) {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => $resultadoNivel]);
                    exit;
                }else{
                    http_response_code(200);
                    echo json_encode($resultadoNivel);
                }
                exit;
            }

    default:
        http_response_code(405);
        echo json_encode(array("status" => "error", "message" => "Método no permitido"));
        break;
}
?>
