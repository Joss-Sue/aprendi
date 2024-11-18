<?php

include "../models/usuarioModel.php";
session_start();



switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            /*if((isset($_COOKIE['correo']))  && (!isset($_SESSION['usuario_id']))){

                $correo = $_COOKIE["correo"];
                $contrasena = $_COOKIE["contrasena"];
                
                UsuarioClass::matchLoginCookie($correo, $contrasena);

            }*/
            //http://localhost/aprendiv1/api/UsuariosController.php/?id=1
            if (isset($_GET['id'])) {
                $usuarioRespuesta=UsuarioClass::buscarUsuarioByID($_GET['id']);
                if(UsuarioClass::buscarUsuarioByID($_GET['id'])==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun usuario encontrado"));
                    exit;
                }else{
                    http_response_code(200);
                    echo json_encode($usuarioRespuesta);
                }
            } else {
                // Si no se pasa 'id', obtener todos los usuarios.
                $todosUsuarios = UsuarioClass::buscarTodosLosUsuarios();
                if ($todosUsuarios == null) {
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "no se encontraron usuarios"));
                    exit;
                } else {
                    http_response_code(200);
                    echo json_encode($todosUsuarios);
                }
            }
        }
        break;
    case 'POST':
        {
            //ejemplo json {"correo":"yo@hotmail.com","contrasena":"123"}
            $data = json_decode(file_get_contents('php://input'), true);

            if(isset($_SERVER['HTTP_ACTION']) && $_SERVER['HTTP_ACTION'] == 'Login'){
               
                extract($data);
    
                if(empty($correo) || empty($contrasena)){
                    http_response_code(400);
                    echo json_encode(array("message" => "algun dato vacio", "error" => "empty"));
                    exit;
                }

                $resultadoFuncion = UsuarioClass::matchLogin($correo, $contrasena);

                 if ($resultadoFuncion[0]){
                    http_response_code(200);
                    echo json_encode(array("status" => "success", "message" => $resultadoFuncion[1]));
                   }else{
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => $resultadoFuncion[1]));
                    }exit;
             
            }else{
                //ejemplo json {"correo": "gato@animal.com","contrasena": "123","nombre": "CAT","rol":"vendedor","direccion":"calle 123"}
                
                extract($data);
                if(empty($correo) || empty($contrasena) || empty($nombre) || empty($rol) || empty($genero) || empty($fecha_nac) || empty($imagen)){
                    echo json_encode(array("message" => "algun dato vacio", "error" => "empty"));
                    exit;
                }
                

                $resultadoFuncion = UsuarioClass::registrarUsuario($correo, $contrasena, $nombre, $rol, $genero, $fecha_nac, $imagen);

                if ($resultadoFuncion[0]){
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => $resultadoFuncion[1]));
                }else{
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => $resultadoFuncion[1]));
                }
                }
                break;
            }
    case 'PUT':
        {
            //ejemplo json {"id":"3","correo": "gato@animal.com","contrasena": "234","nombre": "CAT"}
            $data = json_decode(file_get_contents('php://input'), true);
            
            extract($data);

            if(empty($correo) || empty($contrasena) || empty($id) || empty($nombre) || empty($imagen)){
                echo "error en correo o contra o id";
                exit;
            }

            $resultadoFuncion = UsuarioClass::editarUsuario($id, $correo, $contrasena, $nombre, $imagen);
            if ($resultadoFuncion[0]){
                http_response_code(200);
                echo json_encode(array("status" => "success", "message" => $resultadoFuncion[1]));
               }else{
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => $resultadoFuncion[1]));
            }
            break;
        }
        case 'PATCH':
            {
                // Leer la entrada JSON
                $data = json_decode(file_get_contents('php://input'), true);
                
                // Obtener el ID del usuario de los parámetros de la URL
                $id = (isset($_GET['id'])) ? $_GET['id'] : null;
        
                // Verificar que se proporcionó el ID
                if (empty($id)) {
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ID de usuario requerido"));
                    exit;
                }
        
                // Verificar que se proporcionó el nuevo estado
                $estado = isset($data['estado']) ? $data['estado'] : null;
        
                if (is_null($estado)) {
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "Estado requerido para actualizar el usuario"));
                    exit;
                }
        
                // Llamar a la función de modelo para actualizar el estado del usuario
                $resultado = UsuarioClass::actualizarEstadoUsuario($id, $estado);
        
                if ($resultado) {
                    http_response_code(200);
                    echo json_encode(array("status" => "success", "message" => "Usuario actualizado con éxito"));
                } else {
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "Error al actualizar el usuario"));
                }
                break;
            }
        
    case 'DELETE':
        {
            /*ejemplo json{"id":2}*/
            $data = json_decode(file_get_contents('php://input'), true);
            $id = (isset($data['id']))?($data['id']):null;
            
            if(empty($id)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "id vacio"));
                exit;
            }

            $resultadoFuncion = UsuarioClass::eliminarUsuario($data['id']);
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