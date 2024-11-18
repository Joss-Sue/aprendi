<?php
include_once '../config/bd_conexion.php';


class CategoriaClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function registrarCategoria($nombre, $descripcion, $createdBy){
        self::inicializarConexion();
        
        try{
        $sqlInsert="call insertarcategoria (:nombre, :descripcion, :createdBy);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute(array(
        ':nombre'=>$nombre,
        ':descripcion'=>$descripcion,
        ':createdBy'=>$createdBy
        ));

        return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "La categoria ya ha sido registrada.";
                return array(false, $cadena);
            } else {
                return array(false, "Error al crear categoria: " . $e->getMessage());
            }
        }
    }

    static function editarCategoria($id, $nombre, $descripcion, $createdBy){
        self::inicializarConexion();
        $categoria = CategoriaClass::buscarCategoriaByID($id);
        
    
        if($categoria==null) {
           return array(false,"error en id");
        }
        try{
            $sqlUpdate="update categorias set nombre= :nombre, descripcion= :descripcion, usuario_creador_id= :createdBy where id= :id ";
            $sentencia2 = self::$conexion-> prepare($sqlUpdate);
            $sentencia2 -> execute(array('nombre'=>$nombre,
                                    'descripcion'=>$descripcion,
                                    'createdBy'=>$createdBy,
                                    'id'=>$id));
            return array(true,"actualizado con exito");
        }catch(PDOException $e){
            return array(false, "Error al editar categoria: " . $e->getMessage());
        }
        

        return array(true,"actualizado con exito");
                                
    }

    static function eliminarCategoria($id){
        self::inicializarConexion();
        $categoria = CategoriaClass::buscarCategoriaByID($id);
        
    
        if($categoria==null) {
           return array(false, "error en id");
        }
        try{
        $sqlUpdate="update categorias set estado = 0 where id = :id";
        $sentencia2 = self::$conexion-> prepare($sqlUpdate);
        $sentencia2 -> execute(['id'=>$id]);
            //echo '<script>alert("You have been logged out.")</script>;'
            return array(true, "eliminado exitoso");
        }catch(PDOException $e){
            return array(false, "Error al eliminar categoria: " . $e->getMessage());
        }
                                
    }

    static function buscarCategoriaByID($id){
        
        self::inicializarConexion();
        $sql="select * from categorias where id=:id";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id'=>$id]);
    
        $categoria = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$categoria) {
           return null;
        }else{
            return $categoria;
        }
    }

    static function buscarAllCategorias(){
        
        self::inicializarConexion();
        $sql="select * from categorias where estado=1";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute();
        
    
        $categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$categorias) {
           return null;
        }else{
            return $categorias;
        }
    }



}
?>