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
        $sqlInsert="CALL registrarNivel (:curso_id, :nivel, :url_video, :descripcion);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute(array(
        ':curso_id'=>$curso_id,
        ':nivel'=>$nivel,
        ':url_video'=>$url_video,
        ':descripcion'=>$descripcion
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

    static function editarNivel($curso_id, $nivel, $descripcion, $url_video){
        self::inicializarConexion();
        $niveles = NivelClass::buscarNivelByID($curso_id, $nivel);
        
    
        if($niveles==null) {
           return array(false,"error en id");
        }
        try{
            $sqlUpdate="update niveles set nivel= :nivel, descripcion = :descripcion, url_video = :url_video where curso_id = :curso_id and nivel = :nivel;";
            $sentencia2 = self::$conexion-> prepare($sqlUpdate);
            $sentencia2 -> execute(array('nivel'=>$nivel,
                                    'descripcion'=>$descripcion,
                                    'url_video'=>$url_video,
                                'curso_id'=>$curso_id,
                            'nivel'=>$nivel));
            return array(true,"actualizado con exito");
        }catch(PDOException $e){
            return array(false, "Error al editar el nivel: " . $e->getMessage());
        }
        

        return array(true,"actualizado con exito");
                                
    }

    static function eliminarNivel($curso_id, $nivel){
        self::inicializarConexion();
        $categoria = NivelClass::buscarNivelByID($curso_id, $nivel);
        
    
        if($categoria==null) {
           return array(false, "error en id");
        }
        try{
        $sqlUpdate="CALL eliminar_nivel( :curso_id, :nivel);";
        $sentencia2 = self::$conexion-> prepare($sqlUpdate);
        $sentencia2 -> execute(['curso_id'=>$curso_id,
        'nivel'=>$nivel]);
            //echo '<script>alert("You have been logged out.")</script>;'
            return array(true, "eliminado exitoso");
        }catch(PDOException $e){
            return array(false, "Error al eliminar categoria: " . $e->getMessage());
        }
                                
    }

    static function buscarNivelByID($curso_id, $nivel){
        
        self::inicializarConexion();
        $sql="CALL buscar_nivel_id (:curso_id, :nivel);";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['curso_id'=>$curso_id,
        'nivel'=>$nivel]);
    
        $categoria = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$categoria) {
           return null;
        }else{
            return $categoria;
        }
    }

    static function buscarAllNiveles($id){
        
        self::inicializarConexion();
        $sql="CALL buscar_all_niveles(:id)";
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