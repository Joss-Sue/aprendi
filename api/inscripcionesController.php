<?php
include "../models/inscripcionesModel.php";
session_start();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            //http://localhost/aprendi/api/inscripcionesController.php/?id_curso=1&id_estudiante=1 --niveles por curso (ver niveles)
            if (isset($_GET['curso_id'] ) && isset($_GET['estudiante_id'] ) ) {
                $nivelRespuesta = InscripcionesClass::buscarInscripcionByID($_GET['curso_id'], $_GET['estudiante_id']);
                if($nivelRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun usuario encontrado"));
                }else{
                    http_response_code(200);
                    echo json_encode($nivelRespuesta);
                }exit;
            } elseif (isset($_GET['id'])){
                //http://localhost/aprendi/api/nivelesController.php/?id=1 --nivel (editar nivel)
                    $nivelRespuesta = InscripcionesClass::buscarAllInscripciones($_GET['id']);
                    if($nivelRespuesta==null){
                        http_response_code(400);
                        echo json_encode(array("status" => "error", "message" => "ningun usuario encontrado"));
                    }else{
                        http_response_code(200);
                        echo json_encode($nivelRespuesta);
                    }exit;
            } elseif (isset($_GET['curso_id'])){
                //http://localhost/aprendi/api/nivelesController.php/?id=1 --nivel (editar nivel)
                    $nivelRespuesta = InscripcionesClass::buscarAllInscripcionesIdcurso($_GET['curso_id']);
                    if($nivelRespuesta==null){
                        http_response_code(400);
                        echo json_encode(array("status" => "error", "message" => "ningun curso encontrado"));
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

            //http://localhost/aprendi/api/inscripcionesController.php/
            //{"curso_id": 3, "estudiante_id":1, "precio_pagado":"299.99", "tipo_pago":"tarjeta"}

            $data = json_decode(file_get_contents('php://input'), true);

            extract($data);


                if(empty($curso_id) || empty($estudiante_id) || empty($precio_pagado) || empty($tipo_pago)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                    exit;
                }

                $resultadoFuncion = InscripcionesClass::registrarInscripcion($curso_id, $estudiante_id, $precio_pagado, $tipo_pago);

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
            /*ejemplo json{"curso_id": 1, "estudiante_id": 1, "tipo": "tarjeta"}
            tipo puede ser [ultima] para cada que entra al curso,[terminacion] paracuando termine el curso */ 
            $data = json_decode(file_get_contents('php://input'), true);

            extract($data);
            
            if(empty($curso_id) || empty($estudiante_id) || empty($tipo)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                exit;
            }

            $resultadoFuncion = InscripcionesClass::editarInscripcion($curso_id, $estudiante_id, $tipo, $progreso);
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