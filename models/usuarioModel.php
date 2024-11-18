<?php
include_once '../config/bd_conexion.php';

class UsuarioClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function matchLogin($correo, $contrasena){
        self::inicializarConexion();
        $sql="call login_usuario(:correo);";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['correo'=>$correo]);
    
        $usuario = $sentencia->fetch();
        
        
    
        if(!$usuario) {
            //echo "error en correo";
           return false;
        }  
    
        if($contrasena == $usuario["contrasena"]){
            
            $_SESSION['usuario_id']=$usuario["id"];
            $_SESSION['usuario_tipo']=$usuario["rol"];
            $_SESSION['usuario_nombre']=$usuario["nombre"];
            /*echo'<script type="text/javascript">
            alert("'.$_SESSION['usuario_tipo'].'");
            </script>';*/
            //echo $_SESSION['usuario_nombre'];
            //echo $_SESSION['usuario_id'];
            //header('Location: index.php');
            //echo'<script>
            //window.location.href="home.php";
            //</script>';
            
            
        }else{
            
            //echo "Error en la contraseña o correo";
            //print_r($usuarios["CONTRASENA"] . "serve");
            //print_r($contrasena . "input");
            return array(false, "error en la contrasena o correo");
        }
    
        //echo "todo bien";
        return array(true, "login correcto");
    }

    static function registrarUsuario($correo, $contrasena, $nombre, $rol, $genero, $fecha_nac, $imagen)
{
    self::inicializarConexion();
    // Decodificar la imagen Base64
    $imagenBinario = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagen));

    try {
        $sqlInsert = "CALL insertarusuario (:correo, :contrasena, :nombre, :rol, :genero, :fecha_nac, :foto);";
        $consultaInsert = self::$conexion->prepare($sqlInsert);
        
        $consultaInsert->bindValue(':correo', $correo, PDO::PARAM_STR);
        $consultaInsert->bindValue(':contrasena', $contrasena, PDO::PARAM_STR);
        $consultaInsert->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consultaInsert->bindValue(':rol', $rol, PDO::PARAM_STR);
        $consultaInsert->bindValue(':genero', $genero, PDO::PARAM_STR);
        $consultaInsert->bindValue(':fecha_nac', $fecha_nac, PDO::PARAM_STR);

        $consultaInsert->bindValue(':foto', $imagenBinario, PDO::PARAM_LOB);

        $consultaInsert->execute();
        
        return array(true, "Insertado con éxito");

    } catch (PDOException $e) {
        // Manejo de errores
        if ($e->errorInfo[1] == 1062) {
            $cadena = "El correo electrónico ya está en uso.";
            return array(false, $cadena);
        } else {
            // Otro tipo de error
            return array(false, "Error al insertar usuario: " . $e->getMessage());
        }
    }
}

    static function editarUsuario($id, $correo, $contrasena, $nombre, $imagen){
        self::inicializarConexion();
        $usuario = UsuarioClass::buscarUsuarioByID($id);
        
        if($usuario==null) {
           return array(false,"error en id");
        }

        $imagenBinario = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagen));


        try{
        $sqlUpdate="call editar_usuario(:correo, :contrasena, :nombre, :id)";
        $sentencia = self::$conexion-> prepare($sqlUpdate);

        $sentencia->bindValue(':correo', $correo, PDO::PARAM_STR);
        $sentencia->bindValue(':contrasena', $contrasena, PDO::PARAM_STR);
        $sentencia->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $sentencia->bindValue(':id', $id, PDO::PARAM_INT);

        $sentencia -> execute();

        return array(true,"actualizado con exito");
        }catch(PDOException $e){
        return array(false, "Error al actualizar usuario: " . $e->getMessage());
        }
                                
    }

    static function eliminarUsuario($id){
        self::inicializarConexion();
        ;
        
    
        if(UsuarioClass::buscarUsuarioByID($id)==null) {
           return array(false, "error en id");
        }

        $sqlUpdate="call eliminar_usuario(:id);";
        $sentencia2 = self::$conexion-> prepare($sqlUpdate);
        $sentencia2 -> execute(['id'=>$id]);

        setcookie('correo','',-1, '/');
        setcookie('contrasena','',-1, '/');
            //echo '<script>alert("You have been logged out.")</script>;'
            if(session_status()==PHP_SESSION_NONE){
                session_start();
            }
            session_destroy();

        return array(true, "eliminado exitoso");
                                
    }

    static function buscarUsuarioByID($id){
        
        self::inicializarConexion();
        $sql="call buscar_usuario(:id);";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id'=>$id]);
    
        $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$usuario) {
           return null;
        }else{
            return $usuario;
        }
    }

    static function buscarTodosLosUsuarios() {
        self::inicializarConexion();
    
        $sql = "SELECT * FROM Usuarios"; //WHERE estado = :estado ['estado' => 1]
        $sentencia = self::$conexion->prepare($sql);
        $sentencia->execute();
    
        $usuarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if (!$usuarios) {
            return null;
        } else {
            return $usuarios;
        }
    }

    static function actualizarEstadoUsuario($id, $estado) {
        self::inicializarConexion();
    
        $sql = "UPDATE Usuarios SET estado = :estado WHERE id = :id";
        $sentencia = self::$conexion->prepare($sql);
        $resultado = $sentencia->execute(['estado' => $estado, 'id' => $id]);
    
        return $resultado;
    }
    
    
}

?>