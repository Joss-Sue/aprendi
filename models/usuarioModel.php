<?php
include_once '../config/bd_conexion.php';


class UsuarioClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function matchLogin($correo, $contrasena){
        self::inicializarConexion();
        $sql="select * from usuarios where correo = :correo";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['correo'=>$correo]);
    
        $usuario = $sentencia->fetch();
        
        
    
        if(!$usuario) {
            echo "error en correo";
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
            
            echo "Error en la contraseña o correo";
            //print_r($usuarios["CONTRASENA"] . "serve");
            //print_r($contrasena . "input");
            return;
        }
    
        //echo "todo bien";
        return true;
    }

    static function registrarUsuario($correo, $contrasena, $nombre, $rol){
        self::inicializarConexion();
        //echo $rol;
        try{
        $sqlInsert="insert into usuarios (correo, contrasena, nombre, rol) values (:correo, :contrasena, :nombre, :rol);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute(array(
        ':correo'=> $correo,
        ':contrasena'=>$contrasena,
        ':nombre'=>$nombre,
        ':rol'=>$rol
        ));
    
       
        //setcookie('correo',$correo,time()+3600, "/");
        //setcookie('contrasena',$contrasena,time()+3600, "/");
        return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "El correo electrónico ya está en uso.";
                return array(false, $cadena);
            } else {
                // Otro tipo de error
                return array(false, "Error al insertar usuario: " . $e->getMessage());
            }
        }
    }

    static function editarUsuario($id, $correo, $contrasena, $nombre){
        self::inicializarConexion();
        $usuario = UsuarioClass::buscarUsuarioByID($id);
        
    
        if($usuario==null) {
           return array(false,"error en id");
        }

        try{
        $sqlUpdate="update usuarios set correo = :correo, contrasena = :contrasena, nombre= :nombre where id= :id ";
        $sentencia2 = self::$conexion-> prepare($sqlUpdate);
        $sentencia2 -> execute(['correo'=>$correo,
                                'contrasena'=>$contrasena,
                                'nombre'=>$nombre,
                                'id'=>$id]);

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

        $sqlUpdate="update usuarios set estado = 0 where id = :id";
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
        $sql="select * from usuarios where id=:id";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id'=>$id]);
    
        $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$usuario) {
           return null;
        }else{
            return $usuario;
        }
    }

}

?>