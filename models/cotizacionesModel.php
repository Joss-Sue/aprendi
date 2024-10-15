<?php
include_once '../config/bd_conexion.php';
session_start();

class CotizacionClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function registrarCotizacion($idProducto, $idCliente, $idVendedor, $precioReal, $precioSolicitado, $Estatus){
        self::inicializarConexion();
        
        try{
        $sqlInsert="insert into cotizaciones (ProductoId, ClienteId, VendedorId, PrecioProducto, PrecioSolicitado, Estatus)
        values (:idProducto, :idCliente, :idVendedor, :precioReal, :precioSolicitado, :Estatus);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute([
                                    ':idProducto'=>$idProducto,
                                    ':idCliente'=>$idCliente,
                                    ':idVendedor'=>$idVendedor,
                                    ':precioReal'=>$precioReal,
                                    ':precioSolicitado'=>$precioSolicitado,
                                    ':Estatus'=>$Estatus]);
        return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "La cotizacion ya ha sido registrada.";
                return array(false, $cadena);
            } else {
                return array(false, "Error al crear la cotiacion: " . $e->getMessage());
            }
        }
    }

    static function getVendedorCotizaciones($vendedorId){
        self::inicializarConexion();
        $sql= "SELECT c.*, car.idCart, p.nombreProd, p.descripcionProd  FROM Cotizaciones c INNER JOIN Carritos car ON c.ClienteId = car.usuarioCart INNER JOIN Productos p ON c.ProductoId = p.idProd WHERE c.VendedorId = :vendedorId";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia->bindValue(':vendedorId',$vendedorId, PDO::PARAM_INT);
        $sentencia -> execute();
    
        $cotizaciones = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$cotizaciones) {
           return null;
        }else{
            return $cotizaciones;
        }
    }

    static function getClienteCotizaciones($ClienteId){
        self::inicializarConexion();
        $sql= "SELECT c.*, car.idCart, p.nombreProd, p.descripcionProd  FROM Cotizaciones c INNER JOIN Carritos car ON c.ClienteId = car.usuarioCart INNER JOIN Productos p ON c.ProductoId = p.idProd WHERE c.ClienteId = :ClienteId";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia->bindValue(':ClienteId',$ClienteId, PDO::PARAM_INT);
        $sentencia -> execute();
    
        $cotizaciones = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$cotizaciones) {
           return null;
        }else{
            return $cotizaciones;
        }
    }

    static function estatusCotizacion($idCotizacion, $estatus){
        self::inicializarConexion();

        try{
            $sqlInsert="update cotizaciones set Estatus = :estatus where id = :idCotizacion";
            $consultaInsert= self::$conexion->prepare($sqlInsert);
            $consultaInsert->execute([
                ':idCotizacion'=>$idCotizacion,
                ':estatus'=>$estatus
                                    ]);
            return array(true,"insertado con exito");
            
            }catch(PDOException $e){
                if ($e->errorInfo[1] == 1062) {
                    $cadena = "La cotizacion ya ha sido registrada.";
                    return array(false, $cadena);
                } else {
                    return array(false, "Error al crear la cotiacion: " . $e->getMessage());
                }
            }
    }


}
?>