<?php
include_once '../config/bd_conexion.php';


class ValoracionesClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function registrarProducto($comentario,$valoracion, $idProdVal,$idUsuarioVal){
        self::inicializarConexion();
        
        try{
            $sqlInsert="insert into comentarios (contenido, calificacion, curso_id, usuario_id) values (:comentario, :valoracion, :idProdVal, :idUsuarioVal)";
            $consultaInsert= self::$conexion->prepare($sqlInsert);
            $consultaInsert->execute([
                                    ':comentario' => $comentario,
                                    ':valoracion' => $valoracion,
                                    ':idProdVal' => $idProdVal,
                                    ':idUsuarioVal' => $idUsuarioVal
                                    ]);
            return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "El comentario esta repetido.";
                return array(false, $cadena);
            } else {
                return array(false, "Error al agregar comentario: " . $e->getMessage());
            }
        }
    }

    static function editarProducto($id,$comentario, $valoracion){
        self::inicializarConexion();
        
        try{
            $sqlUpdate="update comentarios set contenido = :comentario, calificacion = :valoracion where id = :id;";
            $sentencia = self::$conexion-> prepare($sqlUpdate);
            $sentencia -> execute([ ':id'=>$id,
                                    ':comentario'=>$comentario,
                                    ':valoracion'=>$valoracion
                                    ]);
            return array(true,"actualizado con exito");
        }catch(PDOException $e){
            return array(false, "Error al editar la valoracion: " . $e->getMessage());
        }
        

        return array(true,"actualizado con exito");
                                
    }

    static function eliminarProducto($id){
        self::inicializarConexion();
        
        try{
        $sqlUpdate="update comentarios set estado = 0 where id = :id";
        $sentencia2 = self::$conexion-> prepare($sqlUpdate);
        $sentencia2 -> execute(['id'=>$id]);
            //echo '<script>alert("You have been logged out.")</script>;'
            return array(true, "eliminado exitoso");
        }catch(PDOException $e){
            return array(false, "Error al eliminar la valoracion: " . $e->getMessage());
        }
                                
    }

    static function buscarProductoByID($id_curso, $id_usuario){
        
        self::inicializarConexion();
        $sql="select * from comentarios where curso_id=:id_curso and usuario_id = :id_usuario and estado = 1";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id_curso'=>$id_curso,
                            'id_usuario'=>$id_usuario
                            ]);
    
        $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$producto) {
           return null;
        }else{
            return $producto;
        }
    }

    static function buscarAllProductos($id){
        self::inicializarConexion();
        $sql="select* from comentarios where estado = 1 and curso_id= :id order by id desc";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute([':id'=> $id]);
        //$sentencia->bindValue(':pagina', $pagina, PDO::PARAM_INT);
        $sentencia->execute();
        
    
        $productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$productos) {
           return null;
        }else{
            return $productos;
        }
    }
    static function buscarAllProductosEliminados($id){
        self::inicializarConexion();
        $sql="select* from comentarios where estado = 0 and curso_id= :ideli order by id desc";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute([':ideli'=> $id]);
        //$sentencia->bindValue(':pagina', $pagina, PDO::PARAM_INT);
        $sentencia->execute();
        
    
        $productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$productos) {
           return null;
        }else{
            return $productos;
        }
    }

}
?>