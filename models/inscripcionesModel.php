<?php
include_once '../config/bd_conexion.php';


class InscripcionesClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function registrarInscripcion($curso_id, $estudiante_id, $precio_pagado, $tipo_pago){
        self::inicializarConexion();
        
        try{
        $sqlInsert="insert into inscripciones (curso_id, estudiante_id, precio_pagado, tipo_pago) values (:curso_id, :estudiante_id, :precio_pagado, :tipo_pago);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute(array(
        ':curso_id'=>$curso_id,
        ':estudiante_id'=>$estudiante_id,
        ':precio_pagado'=>$precio_pagado,
        ':tipo_pago'=>$tipo_pago
        ));

        return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "La categoria ya ha sido registrada.";
                return array(false, $cadena);
            } else {
                return array(false, "Error al crear la inscripción: " . $e->getMessage());
            }
        }
    }

    static function editarInscripcion($curso_id, $estudiante_id, $tipo, $progreso){
        self::inicializarConexion();

        try{
            if ($tipo=="ultima"){
                $tipo_fecha= "fecha_ultima";
            }elseif($tipo=="terminacion"){
                $tipo_fecha= "fecha_terminacion";
            }else{
                return array(false,"error en tipo");
            }

            $sqlUpdate="update inscripciones set $tipo_fecha = current_timestamp, progreso_curso = :progreso where curso_id = :curso_id and estudiante_id = :estudiante_id";
            $sentencia = self::$conexion-> prepare($sqlUpdate);
            $sentencia -> execute(array(
                                    ':curso_id'=>$curso_id,
                                    ':estudiante_id'=>$estudiante_id,
                                    ':progreso' => $progreso
                                ));
            return array(true,"actualizado con exito");
        }catch(PDOException $e){
            return array(false, "Error al editar el nivel: " . $e->getMessage());
        }
        

        return array(true,"actualizado con exito");
                                
    }

    static function buscarInscripcionByID($curso_id, $estudiante_id){
        
        self::inicializarConexion();
        $sql="CALL buscar_inscripcion_id(:curso_id, :estudiante_id";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['curso_id'=>$curso_id,
        'estudiante_id'=>$estudiante_id]);
    
        $inscripcion = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$inscripcion) {
           return null;
        }else{
            return $inscripcion;
        }
    }

    static function buscarAllInscripciones($id){
        
        self::inicializarConexion();
        $sql="CALL buscar_all_inscripciones(:id);";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id'=>$id]);
        
    
        $inscripcion = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$inscripcion) {
           return null;
        }else{
            return $inscripcion;
        }
    }

    static function buscarAllInscripcionesIdcurso($curso_id){
        
        self::inicializarConexion();
        $sql="CALL buscar_Inscripciones_Idcurso(:curso_id);";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['curso_id'=>$curso_id]);
        
    
        $inscripcion = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$inscripcion) {
           return null;
        }else{
            return $inscripcion;
        }
    }



}
?>