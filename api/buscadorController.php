<?php
//include "../models/valoracionesModel.php";
require_once '../config/bd_conexion.php';
session_start();


switch ($_SERVER['REQUEST_METHOD']) {
    
    case 'GET':
        {
            if(isset($_GET['palabra'])){
            //http://localhost/massivedemo/api/productosController.php/?idCarrito=1
            $conexion=BD::crearInstancia();
            $productosRespuesta = buscarAllProductos($_GET['palabra'], $conexion);
                if($productosRespuesta==null){
                    http_response_code(400);
                    echo json_encode(array("status" => "error", "message" => "ningun producto encontrado"));
                    exit;
                }else{
                    http_response_code(200);
                    echo json_encode($productosRespuesta);
                    exit;
                }
            }elseif(isset($_GET['categoria'])){
                $conexion=BD::crearInstancia();
                $productosRespuesta = buscarByCategoria($_GET['categoria'], $conexion);
                    if($productosRespuesta==null){
                        http_response_code(400);
                        echo json_encode(array("status" => "error", "message" => "ningun producto encontrado"));
                        exit;
                    }else{
                        http_response_code(200);
                        echo json_encode($productosRespuesta);
                        exit;
                    }
            }
        http_response_code(400);
        echo json_encode(array("status" => "error", "message" => "ningun producto encontrado"));

        break;
        }
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}

function buscarAllProductos($palabra, $conexion){

    $sql="call buscarproductos(:palabra);";
    $sentencia = $conexion-> prepare($sql);
    $sentencia->bindValue(':palabra', $palabra, PDO::PARAM_STR);
    $sentencia -> execute();
    //$sentencia->bindValue(':pagina', $pagina, PDO::PARAM_INT);
    //$sentencia->execute();
    

    $productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    if(!$productos) {
       return null;
    }else{
        return $productos;
    }
}

function buscarByCategoria($categoria, $conexion){

    $sql="SELECT p.* 
    FROM productos AS p
    JOIN categorias AS c ON p.categoriaProd = c.id
    WHERE c.nombre = :categoria;";
    $sentencia = $conexion-> prepare($sql);
    $sentencia->bindValue(':categoria', $categoria, PDO::PARAM_STR);
    $sentencia -> execute();
    //$sentencia->bindValue(':pagina', $pagina, PDO::PARAM_INT);
    //$sentencia->execute();
    

    $productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    if(!$productos) {
       return null;
    }else{
        return $productos;
    }
}

?>