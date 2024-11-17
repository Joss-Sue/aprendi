<?php
include '../models/estudiantesNivelesModel.php';
session_start();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            // Obtener los niveles completados por un estudiante en un curso específico
            if (isset($_GET['curso_id']) && isset($_GET['estudiante_id'])) {
                $nivelesCompletados = EstudiantesNivelesClass::buscarEstudianteNiveles($_GET['curso_id'], $_GET['estudiante_id']);
                
                if ($nivelesCompletados == null) {
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "Ningún nivel encontrado"));
                } else {
                    http_response_code(200);
                    echo json_encode($nivelesCompletados);
                }
                exit;
            } else {
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "Parámetros inválidos"));
            }
        }
        break;
        case 'POST':
            {
                // {"curso_id": 1, "nivel_id": 2, "estudiante_id": 6, "progreso": 50}
                $data = json_decode(file_get_contents('php://input'), true);
                
                extract($data);
            
                if (empty($curso_id) || empty($nivel_id) || empty($estudiante_id)) {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
                    exit;
                }
            
                $resultadoNivel = EstudiantesNivelesClass::insertarNivelEstudiante($curso_id, $nivel_id, $estudiante_id);
                if (!$resultadoNivel[0]) {
                    http_response_code(400);
                    echo json_encode(["status" => "error", "message" => $resultadoNivel[1]]);
                    exit;
                }
                exit;
            }

    default:
        http_response_code(405);
        echo json_encode(array("status" => "error", "message" => "Método no permitido"));
        break;
}
?>
