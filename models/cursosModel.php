<?php
include_once '../config/bd_conexion.php';
session_start();

class CursoClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function registrarCurso($titulo, $descripcion, $costo, $instructor, $categoria){
        self::inicializarConexion();
        
        try{
        $sqlInsert="insert into cursos (titulo, descripcion, costo, instructor_id, categoria_id)
        values (:titulo, :descripcion, :costo, :instructor, :categoria);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute([
                                    ':titulo'=>$titulo,
                                    ':descripcion'=>$descripcion,
                                    ':costo'=>$costo,
                                    ':instructor'=>$instructor,
                                    ':categoria'=>$categoria]);
        return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "La categoria ya ha sido registrada.";
                return array(false, $cadena);
            } else {
                return array(false, "Error al crear el producto: " . $e->getMessage());
            }
        }
    }

    static function editarCurso($id, $titulo, $descripcion, $costo, $categoria){
        self::inicializarConexion();
       
        $curso = CursoClass::buscarCursoByID($id);
        
    
        if($curso==null) {
           return array(false,"error en id");
        }

        try{
            $sqlUpdate="update cursos set titulo = :titulo, descripcion = :descripcion, 
            costo = :costo, categoria_id = :categoria 
            where id= :id;";
            $sentencia = self::$conexion-> prepare($sqlUpdate);
            $sentencia -> execute([ ':id'=>$id,
                                    ':titulo'=>$titulo,
                                    ':descripcion'=>$descripcion,
                                    ':costo'=>$costo,
                                    ':categoria'=>$categoria
                                    ]);
            return array(true,"actualizado con exito");
        }catch(PDOException $e){
            return array(false, "Error al editar categoria: " . $e->getMessage());
        }
                                
    }

   

    static function eliminarCurso($id){
        self::inicializarConexion();
        $categoria = CursoClass::buscarCursoByID($id);
        
    
        if($categoria==null) {
           return array(false, "error en id");
        }
        try{
        $sqlUpdate="update cursos set estado = 0 where id = :id";
        $sentencia2 = self::$conexion-> prepare($sqlUpdate);
        $sentencia2 -> execute(['id'=>$id]);
            //echo '<script>alert("You have been logged out.")</script>;'
            return array(true, "eliminado exitoso");
        }catch(PDOException $e){
            return array(false, "Error al eliminar: " . $e->getMessage());
        }
                                
    }

    static function buscarCursoByID($id){
        
        self::inicializarConexion();
        $sql="select * from cursos where id=:id and estado = 1";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id'=>$id]);
    
        $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$producto) {
           return null;
        }else{
            return $producto;
        }
    }

    static function contarFilas($tipo){
        
        self::inicializarConexion();
        $arraySentencias=array("productosByAll"=>"select count(*) as filas from productos where activoProd = 1",
                                "productosByAllAdmin"=>"select count(*) as filas from productos where activoProd = 1 and estaListadoProd = 0",
                                );
        $sentencia = self::$conexion-> prepare($arraySentencias["$tipo"]);
        $sentencia -> execute([]);
    
        $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$producto) {
           return null;
        }else{
            return $producto;
        }
    }

    static function contarFilasByID($tipo,$id){
        
        self::inicializarConexion();
        $arraySentencias=array("productosByVendedor"=>"select count(*) as filas from productos where activoProd = 1 and vendedorProd = :id",
                                "productosByAdmin"=>"select count(*) as filas from productos where activoProd = 1 and adminAutoriza = :id",
                                );
        //$sqlSelect = "select count(*) as filas from productos where activoProd = 1 and vendedorProd = :id";
        $sentencia = self::$conexion-> prepare($arraySentencias[$tipo]);
        $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
        $sentencia -> execute();
    
        $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$producto) {
           return null;
        }else{
            return $producto;
        }
    }

    static function buscarAllCursos($pagina){
        $pagina=($pagina-1)*20;
        self::inicializarConexion();
        
        $sql="select * from cursos where estado = 1 order by fecha_creacion desc limit 20 offset :pagina";
        
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia->bindValue(':pagina', $pagina, PDO::PARAM_INT);
        $sentencia->execute();
        
    
        $productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$productos) {
           return null;
        }else{
            return $productos;
        }
    }

    static function buscarByCategoria($pagina,$categoria){
        $pagina=($pagina-1)*20;
        self::inicializarConexion();
        
        $sql="select * from cursos where estado = 1 and categoria_id = :categoria order by fecha_creacion desc limit 20 offset :pagina";
        $sentencia = self::$conexion->prepare($sql);
        $sentencia->bindValue(':pagina', $pagina, PDO::PARAM_INT);
        $sentencia->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $sentencia->execute();
        
        $productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$productos) {
           return null;
        }else{
            return $productos;
        }
    }

    static function buscarAllProductosWithID($pagina,$id){
        $pagina=($pagina-1)*20;
        self::inicializarConexion();
        //$tipo="vendedor";
        
        $sql="select * from cursos where instructor_id = :id order by fecha_creacion desc limit 20 offset :pagina";
        
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia->bindValue(':pagina', $pagina, PDO::PARAM_INT,);
        $sentencia->bindValue(':id',$id, PDO::PARAM_INT);
        $sentencia -> execute();
        
        //$sentencia->execute();
        
    
        $productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$productos) {
           return null;
        }else{
            return $productos;
        }
    }

}
?>