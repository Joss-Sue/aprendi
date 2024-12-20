<?php
include "../models/nivelesModel.php";
session_start();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            if (isset($_GET['id']) && isset($_GET['nivel'])){
                //http://localhost/aprendi/api/nivelesController.php/?id=1&nivel=1 --curso id y nivel (editar nivel)
                    $nivelRespuesta = NivelClass::buscarNivelByID($_GET['id'], $_GET['nivel']);
                    if($nivelRespuesta==null){
                        http_response_code(400);
                        echo json_encode(array("status" => "error", "message" => "ningun usuario encontrado"));
                    }else{
                        http_response_code(200);
                        echo json_encode($nivelRespuesta);
                    }exit;
                //http://localhost/aprendi/api/nivelesController.php/?id_curso=1 --niveles por curso (ver niveles)
                }elseif (isset($_GET['curso_id'])) {
                $nivelRespuesta = NivelClass::buscarAllNiveles($_GET['curso_id']);
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
            /*ejemplo json{"curso_id":1, "nivel":"1", "url_video":"https://www.youtube.com/watch?v=nPABGMj5crU", "descripcion":"modulo donde encontraras"}*/

            //$nombre = $_POST['nombre'];
            //$descripcion = $_POST['descripcion'];
            //$createdBy = $_POST['createdBy'];

            $data = json_decode(file_get_contents('php://input'), true);

            extract($data);

                /* $nombre= (isset($data['nombre']))?$data['nombre']:null;
                $descripcion= (isset($data['descripcion']))?$data['descripcion']:null;
                $createdBy= (isset($data['createdBy']))?$data['createdBy']:null;*/
                
                //var_dump($data);
                //var_dump($createdBy);
                if(empty($curso_id) || empty($nivel) || empty($url_video) || empty($descripcion)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                    exit;
                }

                $resultadoFuncion = NivelClass::registrarNivel($nivel, $url_video, $descripcion, $curso_id);

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
            /*ejemplo json{"id":1, "nivel":"2", "url_video":"https://www.youtube.com/watch?v=nPABGMj5crU", "descripcion":"modulo donde encontraras muchas cosas"}*/
            $data = json_decode(file_get_contents('php://input'), true);

            //$id=(isset($data['id']))?$data['id']:null;
            //$nombre= (isset($data['nombre']))?$data['nombre']:null;
            //$descripcion= (isset($data['descripcion']))?$data['descripcion']:null;
            //$createdBy= (isset($data['createdBy']))?$data['createdBy']:null;

            extract($data);
            
            if(empty($curso_id) || empty($nivel) || empty($descripcion) || empty($url_video)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                exit;
            }

            $resultadoFuncion = NivelClass::editarNivel($curso_id, $nivel, $descripcion, $url_video);
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
            extract($data);

            if(empty($curso_id) || empty($nivel)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "id vacio"));
                exit;
            }

            $resultadoFuncion = NivelClass::eliminarNivel($curso_id, $nivel);
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