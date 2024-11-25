<?php
include_once '../config/bd_conexion.php';
session_start();

class CursoClass{

    public static $conexion;

    public static function inicializarConexion() {
        self::$conexion = BD::crearInstancia();
    }

    static function registrarCurso($titulo, $descripcion, $costo, $instructor, $categoria, $imagen){
        self::inicializarConexion();
        
        $imagenBinario = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagen));

        try{
        $sqlInsert="CALL registrar_curso( :titulo, :descripcion, :costo, :instructor, :categoria, :imagen);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);

        $consultaInsert->bindValue('titulo', $titulo, PDO::PARAM_STR);
        $consultaInsert->bindValue('descripcion', $descripcion, PDO::PARAM_STR);
        $consultaInsert->bindValue('costo', $costo, PDO::PARAM_STR);
        $consultaInsert->bindValue('instructor', $instructor, PDO::PARAM_INT);
        $consultaInsert->bindValue('categoria', $categoria, PDO::PARAM_INT);
        $consultaInsert->bindValue('imagen', $imagenBinario, PDO::PARAM_LOB);

        $consultaInsert->execute();

        return array(true,"insertado con exito");
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "La categoria ya ha sido registrada.";
                return array(false, $cadena);
            } else {
                return array(false, "Error al crear el producto: " . $e->getMessage());
            }
        }
    }

    static function editarCurso($id, $titulo, $descripcion, $costo, $categoria, $imagen){
        self::inicializarConexion();
       
        $curso = CursoClass::buscarCursoByID($id);
        
    
        if($curso==null) {
           return array(false,"error en id");
        }

        $imagenBinario = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagen));

        try{
            $sqlUpdate="CALL editar_curso (:id, :titulo, :descripcion, :costo, :categoria, :imagen;";
            $sentencia = self::$conexion-> prepare($sqlUpdate);
            $sentencia->bindValue('id', $id, PDO::PARAM_INT);
            $sentencia->bindValue('titulo', $titulo, PDO::PARAM_STR);
            $sentencia->bindValue('descripcion', $descripcion, PDO::PARAM_STR);
            $sentencia->bindValue('costo', $costo, PDO::PARAM_STR);
            $sentencia->bindValue('categoria', $categoria, PDO::PARAM_INT);
            $sentencia->bindValue('imagen', $imagenBinario, PDO::PARAM_LOB);

            $sentencia -> execute();
            return array(true,"actualizado con exito");
        }catch(PDOException $e){
            return array(false, "Error al editar categoria: " . $e->getMessage());
        }
                                
    }

   

    static function eliminarCurso($id){
        self::inicializarConexion();
        $categoria = CursoClass::buscarCursoByID($id);
        
    
        if($categoria==null) {
           return array(false, "error en id");
        }
        try{
        $sqlUpdate="update cursos set estado = 0 where id = :id";
        $sentencia2 = self::$conexion-> prepare($sqlUpdate);
        $sentencia2 -> execute(['id'=>$id]);
            //echo '<script>alert("You have been logged out.")</script>;'
            return array(true, "eliminado exitoso");
        }catch(PDOException $e){
            return array(false, "Error al eliminar: " . $e->getMessage());
        }
                           
    }

    static function buscarCursoByID($id) {
        self::inicializarConexion();
        $sql = "CALL buscar_curso_id( :id )";
        $sentencia = self::$conexion->prepare($sql);
        $sentencia->execute(['id' => $id]);
    
        $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
    
        if (!$producto) {
            return null;
        } else {
            if (!empty($producto['imagen'])) {
                $producto['imagen'] = 'data:image/png;base64,' . base64_encode($producto['imagen']);
            }
            return $producto;
        }
    }
    

    /*static function contarFilas($tipo){
        
        self::inicializarConexion();
        $arraySentencias=array("productosByAll"=>"select count(*) as filas from productos where activoProd = 1",
                                "productosByAllAdmin"=>"select count(*) as filas from productos where activoProd = 1 and estaListadoProd = 0",
                                );
        $sentencia = self::$conexion-> prepare($arraySentencias["$tipo"]);
        $sentencia -> execute([]);
    
        $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$producto) {
           return null;
        }else{
            return $producto;
        }
    }

    static function contarFilasByID($tipo,$id){
        
        self::inicializarConexion();
        $arraySentencias=array("productosByVendedor"=>"select count(*) as filas from productos where activoProd = 1 and vendedorProd = :id",
                                "productosByAdmin"=>"select count(*) as filas from productos where activoProd = 1 and adminAutoriza = :id",
                                );
        //$sqlSelect = "select count(*) as filas from productos where activoProd = 1 and vendedorProd = :id";
        $sentencia = self::$conexion-> prepare($arraySentencias[$tipo]);
        $sentencia->bindValue(':id', $id, PDO::PARAM_INT);
        $sentencia -> execute();
    
        $producto = $sentencia->fetch(PDO::FETCH_ASSOC);
        
    
        if(!$producto) {
           return null;
        }else{
            return $producto;
        }
    }*/

    static function buscarAllCursos($pagina) {
        $pagina = ($pagina - 1) * 20;
        self::inicializarConexion();
    
        $sql = "CALL buscar_all_cursos(:pagina)";
        $sentencia = self::$conexion->prepare($sql);
        $sentencia->bindValue(':pagina', $pagina, PDO::PARAM_INT);
        $sentencia->execute();
    
        $productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        // Convertir las imágenes binarias a Base64 en todos los productos
        if ($productos) {
            foreach ($productos as &$producto) {
                if (isset($producto['imagen'])) {
                    $producto['imagen'] = 'data:image/png;base64,' . base64_encode($producto['imagen']);
                }
            }
        }
    
        return $productos ?: null;
    }

    static function buscarByCategoria($pagina,$categoria){
        $pagina=($pagina-1)*20;
        self::inicializarConexion();
        
        $sql="CALL buscar_por_categoria( :categoria , :pagina )";
        $sentencia = self::$conexion->prepare($sql);
        $sentencia->bindValue(':pagina', $pagina, PDO::PARAM_INT);
        $sentencia->bindValue(':categoria', $categoria, PDO::PARAM_INT);
        $sentencia->execute();
        
        $productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$productos) {
           return null;
        }else{
            return $productos;
        }
    }

    static function buscarAllProductosWithID($pagina,$id){
        $pagina = ($pagina - 1) * 20;
        self::inicializarConexion();
        //$tipo="vendedor";
        
        $sql="CALL buscar_cursos_instructor( :id, :pagina)";
        
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia->bindValue(':pagina', $pagina, PDO::PARAM_INT,);
        $sentencia->bindValue(':id',$id, PDO::PARAM_INT);
        $sentencia -> execute();
        
        //$sentencia->execute();
        
    
        $productos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$productos) {
           return null;
        }else{
            return $productos;
        }
    }

    static function buscarCursos($valorBusqueda, $categoria_id, $instructor_id,$fecha_inicio, $fecha_fin){
        self::inicializarConexion();
        
        try{
        $sqlInsert="call BuscarCursos(:valorBusqueda, :categoria_id, :instructor_id, :fecha_inicio, :fecha_fin);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute([
            'valorBusqueda' => $valorBusqueda,
            'categoria_id' => $categoria_id,
            'instructor_id' => $instructor_id,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin
    ]);


        $cursos = $consultaInsert->fetch(PDO::FETCH_ASSOC);

        if ($cursos) {
            foreach ($cursos as &$cursos) {
                if (isset($cursos['imagen'])) {
                    $cursos['imagen'] = 'data:image/png;base64,' . base64_encode($cursos['imagen']);
                }
            }
        }
        
    
        if(!$cursos) {
        return null;
        }else{
            return $cursos;
        }
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "La categoria ya ha sido registrada.";
                return array(false, $cadena);
            } else {
                return array(false, "Error al crear categoria: " . $e->getMessage());
            }
        }
    }

    static function buscarCursosPantallaPrincipal($filtro){
        self::inicializarConexion();
        
        try{
        $sqlInsert="call cursos_pantalla_principal (:filtro);";
        $consultaInsert= self::$conexion->prepare($sqlInsert);
        $consultaInsert->execute([
            'filtro' => $filtro
        ]);


        $cursos = $consultaInsert->fetch(PDO::FETCH_ASSOC);


        if ($cursos) {
            foreach ($cursos as &$cursos) {
                if (isset($cursos['imagen'])) {
                    $cursos['imagen'] = 'data:image/png;base64,' . base64_encode($cursos['imagen']);
                }
            }
        }
        
    
        if(!$cursos) {
        return null;
        }else{
            return $cursos;
        }
        
        }catch(PDOException $e){
            if ($e->errorInfo[1] == 1062) {
                $cadena = "La categoria ya ha sido registrada.";
                return array(false, $cadena);
            } else {
                return array(false, "Error al crear categoria: " . $e->getMessage());
            }
        }
    }

    static function ultimoCursoInstructor($id){

        self::inicializarConexion();
        $sql="call ObtenerUltimoIDPorInstructor(:id);";
        $sentencia = self::$conexion-> prepare($sql);
        $sentencia -> execute(['id' => $id]);
        
    
        $categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);
    
        if(!$categorias) {
           return null;
        }else{
            return $categorias;
        }
    }

}
?>