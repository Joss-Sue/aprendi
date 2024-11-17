<?php
include_once '../config/bd_conexion.php';


class MensajesClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function registrarMensaje($remitente_id, $destinatario_id, $curso_id, $contenido){
        self::inicializarConexion();
        
        try{
        $sqlInsert="insert into mensajes (remitente_id, destinatario_id, curso_id, contenido) values (:remitente_id, :destinatario_id, :curso_id, :contenido);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute(array(
        ':remitente_id'=>$remitente_id,
        ':destinatario_id'=>$destinatario_id,
        ':curso_id'=>$curso_id,
        ':contenido'=>$contenido
        ));

        return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "La categoria ya ha sido registrada.";
                return array(false, $cadena);
            } else {
                return array(false, "Error al enviar el mensaje: " . $e->getMessage());
            }
        }
    }

    static function buscarMesajes($id_curso, $id){
        
        self::inicializarConexion();
        $sql="select * from mensajes where curso_id = :id_curso and (destinatario_id =:id or remitente_id=:id) order by fecha asc";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id'=>$id,
        'id_curso'=>$id_curso]);
    
        $categoria = $sentencia->fetchAll(PDO::FETCH_ASSOC);
        
    
        if(!$categoria) {
           return null;
        }else{
            return $categoria;
        }
    }

}
?>