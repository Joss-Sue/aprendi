<?php
include "../models/certificadosModel.php";
session_start();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            //http://localhost/aprendi/api/certificadosController.php/?id_curso=1&id_estudiante=1 --obtener datos de un certificado especifico
            if (isset($_GET['curso_id']) && isset($_GET['estudiante_id'])) {
                $nivelRespuesta = CertificadoClass::buscarCertificadoByID($_GET['estudiante_id'],$_GET['curso_id']);
                if($nivelRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun certificado encontrado"));
                }else{
                    http_response_code(200);
                    echo json_encode($nivelRespuesta);
                }exit;
            } elseif (isset($_GET['id'])){
                //http://localhost/aprendi/api/nivelesController.php/?id=1 --ver todos los certificados obtenidos por usuario
                    $nivelRespuesta = CertificadoClass::buscarAllCertificados($_GET['id']);
                    if($nivelRespuesta==null){
                        http_response_code(400);
                        echo json_encode(array("status" => "error", "message" => "ningun certificado encontrado"));
                    }else{
                        http_response_code(200);
                        echo json_encode($nivelRespuesta);
                    }exit;
            }else{
                http_response_code(400);
            }
        }
        break;
    case 'POST':
        {
            /*ejemplo json{"curso_id":1, "nivel":"1", "url_video":"https://www.youtube.com/watch?v=nPABGMj5crU", "descripcion":"modulo donde encontraras"}*/

            $data = json_decode(file_get_contents('php://input'), true);

            extract($data);

                if(empty($curso_id) || empty($estudiante_id)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                    exit;
                }

                $resultadoFuncion = CertificadoClass::registrarCertificado($curso_id, $estudiante_id);

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
            
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}

?>