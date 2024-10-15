<?php
include_once '../config/bd_conexion.php';


class ListasProductosClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function registrarProducto($idLista, $idProducto){
        self::inicializarConexion();
        
        try{
        $sqlInsert="call agregarProductoLista(:idLista, :idProducto)";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute([
                                    ':idLista'=>$idLista,
                                    ':idProducto'=>$idProducto
                                ]);
        return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                return array(false, "Error al poner el producto en el carrito: " . $e->getMessage());
            }
        }
    }

    static function eliminarProducto($id){
        self::inicializarConexion();
        $categoria =ListasProductosClass::buscarProductoByID($id);
        
    
        if($categoria==null) {
           return array(false, "error en id");
        }
        try{
        $sqlUpdate="update listasProductos set activo = false where id = :id";
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
        $sql="select * from listasProductos where id=:id and activo = 1";
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
        $sql="SELECT p.nombreProd, p.descripcionProd, p.precioProd, p.idProd, lp.id
        FROM productos AS p
        JOIN listasProductos AS lp ON p.idProd = lp.productoLista
        where lp.idLista = :id and activo = 1";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
        $sentencia -> execute();
        
        
    
        $productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$productos) {
           return null;
        }else{
            return $productos;
        }
    }

}
?>