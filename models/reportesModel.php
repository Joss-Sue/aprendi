<?php
include_once '../config/bd_conexion.php';
session_start();
//fecha esperada mysql YYYY-MM-DD
class ReporteClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    // 0 en idcategoria es igual a sin filtro categoria, estado 1activos, 0 todos
    static function obtenerVentasCursos($id_usuario, $id_categoria, $estado){
        
        self::inicializarConexion();
        $sql= "call sp_lista_cursos_reporte(:id_usuario,:id_categoria, :estado)";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute([
            'id_usuario'=>$id_usuario,
            'id_categoria'=>$id_categoria,
            'estado'=>$estado]);
    
        $reporte = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$reporte) {
           return null;
        }else{
            return $reporte;
        }
    }

    static function obtenerVentasPorCurso($id_curso){
        
        self::inicializarConexion();
        $sql= "call sp_ventas_por_curso(:id_curso)";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id_usuario'=>$id_curso]);
    
        $reporte = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$reporte) {
           return null;
        }else{
            return $reporte;
        }
    }

    static function obtenerKardex($id_usuario, $id_categoria, $estado){
        
        self::inicializarConexion();
        $sql= "call sp_kardex_estudiantes(:id_usuario,:id_categoria, :estado)";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute([
            'id_usuario'=>$id_usuario,
            'id_categoria'=>$id_categoria,
            'estado'=>$estado]);
    
        $reporte = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$reporte) {
           return null;
        }else{
            return $reporte;
        }
    }

    static function obtenerReporteAdminIntructores(){
        
        self::inicializarConexion();
        $sql= "CALL obtener_reporte_adminInstructores();";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute();
    
        $reporte = $sentencia->fetchAll(PDO::FETCH_ASSOC);
        
    
        if(!$reporte) {
           return null;
        }else{
            return $reporte;
        }
    }

    static function obtenerReporteAdminEstudiantes(){
        
        self::inicializarConexion();
        $sql= "CALL obtener_reporte_admin_estudiantes();";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute();
    
        $reporte = $sentencia->fetchAll(PDO::FETCH_ASSOC);
        
    
        if(!$reporte) {
           return null;
        }else{
            return $reporte;
        }
    }

}
?>