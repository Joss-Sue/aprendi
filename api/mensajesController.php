<?php
include "../models/mensajesModel.php";
session_start();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            //http://localhost/aprendi/api/mensajesController.php/?id_curso=1&id=1
            if (isset($_GET['id_curso']) && isset($_GET['id'])) {
                $nivelRespuesta = MensajesClass::buscarMesajes($_GET['id_curso'], $_GET['id']);
                if($nivelRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun usuario encontrado"));
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
            /*ejemplo json{"remitente_id":1, "destinatario_id":"1", "curso_id":1, "contenido":"modulo donde encontraras contenido"}*/

            $data = json_decode(file_get_contents('php://input'), true);

            extract($data);

                if(empty($curso_id) || empty($remitente_id) || empty($destinatario_id) || empty($contenido)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                    exit;
                }

                $resultadoFuncion =  MensajesClass::registrarMensaje($remitente_id, $destinatario_id, $curso_id, $contenido);

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