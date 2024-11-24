<?php
include_once '../config/bd_conexion.php';


class CertificadoClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function registrarCertificado($curso_id, $estudiante_id){
        self::inicializarConexion();
        
        try{
        $sqlInsert="call InsertarCertificado(:estudiante_id, :curso_id);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute(array(
        ':curso_id'=>$curso_id,
        ':estudiante_id'=>$estudiante_id
        ));

        return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "La categoria ya ha sido registrada.";
                return array(false, $cadena);
            } else {
                return array(false, "Error al crear el certificado: " . $e->getMessage());
            }
        }
    }

    static function buscarCertificadoByID($estudiante_id, $curso_id){
        
        self::inicializarConexion();
        $sql="CALL buscar_certificado_id ( :estudiante_id, :curso_id);";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['estudiante_id'=>$estudiante_id,
                            'curso_id'=>$curso_id]);
    
        $certificado = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$certificado) {
           return null;
        }else{
            return $certificado;
        }
    }

    static function buscarAllCertificados($id){
        
        self::inicializarConexion();
        $sql="ALL buscar_all_certificados (:id);";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id'=>$id]);
        
    
        $certificado = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$certificado) {
           return null;
        }else{
            return $certificado;
        }
    }



}
?>