<?php
include_once '../config/bd_conexion.php';


class ListasClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function registrarProducto($idUsuario, $nombre,$descripcion){
        self::inicializarConexion();
        
        try{
        $sqlInsert="insert into listas (usuarioLista, nombre, descripcion) values (:usuarioLista, :nombre, :descripcion)";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute([
                                    ':usuarioLista'=>$idUsuario,
                                    ':nombre'=>$nombre,
                                    ':descripcion'=>$descripcion
                                ]);
        return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                return array(false, "Error al poner el producto en el carrito: " . $e->getMessage());
            }
        }
    }

    static function editarProducto($id,$nombre,$descripcion){
        self::inicializarConexion();
        $producto = ListasClass::buscarProductoByID($id);
        
    
        if($producto==null) {
           return array(false,"error en id");
        }
        try{
            $sqlUpdate="update listas set nombre= :nombre, descripcion = :descripcion where id= :id";
            $sentencia = self::$conexion-> prepare($sqlUpdate);
            $sentencia -> execute([ 
                                    ':id'=>$id,
                                    ':nombre'=>$nombre,
                                    ':descripcion'=>$descripcion
                                ]);
            return array(true,"actualizado con exito");
        }catch(PDOException $e){
            return array(false, "Error al editar el producto del carrito: " . $e->getMessage());
        }
        

        return array(true,"actualizado con exito");
                                
    }

    static function eliminarProducto($id){
        self::inicializarConexion();
        $categoria =ListasClass::buscarProductoByID($id);
        
    
        if($categoria==null) {
           return array(false, "error en id");
        }
        try{
        $sqlUpdate="update listas set activo = false where id = :id";
        $sentencia2 = self::$conexion-> prepare($sqlUpdate);
        $sentencia2 -> execute(['id'=>$id]);
            //echo '<script>alert("You have been logged out.")</script>;'
            return array(true, "eliminado exitoso");
        }catch(PDOException $e){
            return array(false, "Error al eliminar el producto del carrito: " . $e->getMessage());
        }
                                
    }

    static function buscarProductoByID($id){
        
        self::inicializarConexion();
        $sql="select * from listas where id=:id and activo = 1";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id'=>$id]);
    
        $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$producto) {
           return null;
        }else{
            return $producto;
        }
    }

    static function buscarAllProductos($id){

        self::inicializarConexion();
        $sql="select nombre,descripcion from listas where activo = 1 and usuarioLista= :id order by fechaLista desc";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
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

}
?>