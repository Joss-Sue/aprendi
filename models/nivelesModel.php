<?php
include_once '../config/bd_conexion.php';


class NivelClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function registrarNivel($nivel, $url_video, $descripcion, $curso_id){
        self::inicializarConexion();
        
        try{
        $sqlInsert="insert into niveles (curso_id, nivel, url_video, descripcion) values (:curso_id, :nivel, :url_video, :descripcion);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute(array(
        ':curso_id'=>$curso_id,
        ':descripcion'=>$descripcion,
        ':nivel'=>$nivel,
        ':url_video'=>$url_video
        ));

        return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "La categoria ya ha sido registrada.";
                return array(false, $cadena);
            } else {
                return array(false, "Error al crear el nivel: " . $e->getMessage());
            }
        }
    }

    static function editarNivel($id, $nivel, $descripcion, $url_video){
        self::inicializarConexion();
        $niveles = NivelClass::buscarNivelByID($id);
        
    
        if($niveles==null) {
           return array(false,"error en id");
        }
        try{
            $sqlUpdate="update niveles set nivel= :nivel, descripcion = :descripcion, url_video = :url_video where id= :id ";
            $sentencia2 = self::$conexion-> prepare($sqlUpdate);
            $sentencia2 -> execute(array('nivel'=>$nivel,
                                    'descripcion'=>$descripcion,
                                    'url_video'=>$url_video,
                                    'id'=>$id));
            return array(true,"actualizado con exito");
        }catch(PDOException $e){
            return array(false, "Error al editar el nivel: " . $e->getMessage());
        }
        

        return array(true,"actualizado con exito");
                                
    }

    static function eliminarNivel($id){
        self::inicializarConexion();
        $categoria = NivelClass::buscarNivelByID($id);
        
    
        if($categoria==null) {
           return array(false, "error en id");
        }
        try{
        $sqlUpdate="update niveles set estado = 0 where id = :id";
        $sentencia2 = self::$conexion-> prepare($sqlUpdate);
        $sentencia2 -> execute(['id'=>$id]);
            //echo '<script>alert("You have been logged out.")</script>;'
            return array(true, "eliminado exitoso");
        }catch(PDOException $e){
            return array(false, "Error al eliminar categoria: " . $e->getMessage());
        }
                                
    }

    static function buscarNivelByID($id){
        
        self::inicializarConexion();
        $sql="select * from niveles where estado =1 and id =:id";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id'=>$id]);
    
        $categoria = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$categoria) {
           return null;
        }else{
            return $categoria;
        }
    }

    static function buscarAllNiveles($id){
        
        self::inicializarConexion();
        $sql="select * from niveles where estado = 1 and curso_id = :id";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id'=>$id]);
        
    
        $categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$categorias) {
           return null;
        }else{
            return $categorias;
        }
    }



}
?>