<?php
include_once '../config/bd_conexion.php';

class EstudiantesNivelesClass {
    public static $conexion;

    static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    // Método para buscar los niveles completados por un estudiante en un curso
    static function buscarEstudianteNiveles($curso_id, $estudiante_id) {
        self::inicializarConexion();
        $sql = "CALL buscar_estudiante_niveles(:curso_id, :estudiante_id)";
        $sentencia = self::$conexion->prepare($sql);
        $sentencia->execute(['curso_id' => $curso_id, 'estudiante_id' => $estudiante_id]);
        $result = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        return $result ? $result : null;
    }

    static function insertarNivelEstudiante($curso_id, $nivel_id, $estudiante_id) {
        self::inicializarConexion();
        
        try {
            $sqlInsert = "CALL insertar_nivel_estudiante(:curso_id, :nivel_id, :estudiante_id)";
            $sentencia = self::$conexion->prepare($sqlInsert);
            $sentencia->execute([
                ':curso_id' => $curso_id,
                ':nivel_id' => $nivel_id,
                ':estudiante_id' => $estudiante_id
            ]);
            return [true, "Nivel registrado exitosamente"];
        } catch (PDOException $e) {
            return [false, "Error al insertar nivel: " . $e->getMessage()];
        }
    }
}
?>
