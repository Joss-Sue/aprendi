<?php
include "../models/cursosModel.php";


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            
            //http://localhost/massivedemo/api/productosController.php/?id=3&pagina=1
            if (isset($_GET['pagina']) && isset($_GET['id'])){//arreglar usando headers
                $productosRespuesta = CursoClass::buscarAllProductosWithID($_GET['pagina'],$_GET['id']);
                if($productosRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun curso con ese instructor encontrado"));
                }else{
                    http_response_code(200);
                    echo json_encode($productosRespuesta);
                }exit; 
            }elseif (isset($_GET['pagina']) && isset($_GET['categoria'])){
                //http://localhost/massivedemo/api/productosController.php/?pagina=1
                $productosRespuesta = CursoClass::buscarByCategoria($_GET['pagina'],$_GET['categoria']);
                if($productosRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun curso con esa pagina y categoria encontrado"));
                }else{
                    http_response_code(200);
                    echo json_encode($productosRespuesta);
                }exit;
            }elseif (isset($_GET['pagina'])){
                //http://localhost/massivedemo/api/productosController.php/?pagina=1
                $productosRespuesta = CursoClass::buscarAllCursos($_GET['pagina']);
                if($productosRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun curso con esa pagina encontrado"));
                }else{
                    http_response_code(200);
                    echo json_encode($productosRespuesta);
                }exit;
            }elseif (isset($_GET['id'])){
                //http://localhost/massivedemo/api/productosController.php/?id=1
                $cursoRespuesta = CursoClass::buscarCursoByID($_GET['id']);
                if($cursoRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun curso con ese id encontrado"));
                }else{
                    http_response_code(200);
                    echo json_encode($cursoRespuesta);
                }
                exit;
            }
            http_response_code(400);
            echo json_encode(array("status" => "error", "message" => "ningun curso encontrado"));
        }
        break;
    case 'POST':
        {
            /*ejemplo json{"nombre":"Azpe BP-5","descripcion":"Mochila Azpe modelo BP-5",
            "cotizable":0,"precio":259.00,"stock":25,"vendedor":2,"categoria":3}*/

            //$titulo = $_POST['titulo'];
            //$descripcion = $_POST['descripcion'];
            //$costo = $_POST['costo'];
            //$instructor = $_POST['instructor'];
            //$categoria = $_POST['categoria'];

            $data = json_decode(file_get_contents('php://input'), true);

            extract($data);
            
            if(empty($titulo) || empty($descripcion) || empty($costo) || empty($instructor)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                exit;
            }

            $resultadoFuncion = CursoClass::registrarCurso($titulo, $descripcion, $costo, $instructor, $categoria);

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
            /*ejemplo json{"id":"2","nombre":"Wilson Backpack 5","descripcion":"Mochila Wilson modelo WB-03",
            "precio":499.00,"stock":30,"vendedor":"4","categoria":"3"}*/
            //parse_str(file_get_contents("php://input"),$sent_vars);

            $data = json_decode(file_get_contents('php://input'), true);


            

                extract($data);
            
                if(empty($titulo) || empty($descripcion) || empty($costo) || empty($id)){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "algun dato vacio"));
                    exit;
                }

                $resultadoFuncion = CursoClass::editarCurso($id, $titulo, $descripcion, $costo, $categoria);
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
            $id = (isset($data['id']))?($data['id']):null;
            
            if(empty($id)){
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "id vacio"));
                exit;
            }

            $resultadoFuncion = CursoClass::eliminarCurso($data['id']);
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