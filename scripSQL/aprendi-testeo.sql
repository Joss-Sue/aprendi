CREATE DATABASE IF NOT EXISTS APRENDIV1;
USE APRENDIV1;


--drop table niveles;--
--select * from categorias;--
--select * from usuarios;--
--select * from cursos;--
--select * from niveles;--

CREATE TABLE IF NOT EXISTS Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único del usuario',
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre completo del usuario',
    genero VARCHAR(10) NOT NULL COMMENT 'Género del usuario(MASCULINO, FEMENINO, OTRO)',
    fecha_nacimiento DATE NOT NULL COMMENT 'Fecha de nacimiento del usuario',
    foto MEDIUMBLOB COMMENT 'Imagen del perfil del usuario',
    correo VARCHAR(100) UNIQUE NOT NULL COMMENT 'Correo electrónico del usuario',
    contrasena VARCHAR(255)  NOT NULL COMMENT 'Contraseña del usuario (debe ser almacenada de manera segura)',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que el usuario se registró',
    fecha_ultimo_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora del último cambio en la información del usuario',
    rol VARCHAR(20) NOT NULL COMMENT 'Rol del usuario en el portal ( ESTUDIANTES, INSTRUCTOR, ADMINISTRADOR)',
    estado INT DEFAULT 1 COMMENT 'Estado del usuario en el portal (1: Activo, 0: Baja, 2:Deshabilitado, 3: Bloqueado)',
    intentos_fallidos INT DEFAULT 0 COMMENT 'Número de intentos fallidos de inicio de sesión'
);
insert into usuarios (correo, contrasena, nombre, rol) values ("hloa", "12", "nom", "I");
INSERT INTO usuarios (correo, nombre, contrasena, rol) 
VALUES ('admin@correo.com', 'Administrador', MD5('admin123.'), 'administrador');

CREATE TABLE IF NOT EXISTS Categorias (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único de la categoría',
    nombre VARCHAR(100) UNIQUE NOT NULL COMMENT 'Nombre de la categoría',
    descripcion TEXT COMMENT 'Descripción de la categoría',
    usuario_creador_id INT COMMENT 'ID del usuario que creó la categoría',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que se creó la categoría',
	estado INT DEFAULT 1 COMMENT 'Estado de la categoria (1: Activo, 0: Baja)',
    
    FOREIGN KEY (usuario_creador_id) REFERENCES Usuarios(id)
);

CREATE TABLE IF NOT EXISTS Cursos (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único del curso',
    titulo VARCHAR(255) NOT NULL COMMENT 'Título del curso',
    descripcion TEXT COMMENT 'Descripción del curso',
    imagen MEDIUMBLOB COMMENT 'Imagen del curso',
    costo DECIMAL(10, 2) NOT NULL COMMENT 'Costo total del curso',
    estado INT DEFAULT 1 COMMENT 'Estado del curso en el portal (1: Activo, 0: Dado de baja)',
    categoria_id INT NOT NULL COMMENT 'ID de la categoría a la que pertenece el curso',
    instructor_id INT NOT NULL COMMENT 'ID del instructor que ofrece el curso',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que se creó el curso',
    alumnos INT DEFAULT 0 COMMENT 'Cantidad total de alumnos del curso',
    
    FOREIGN KEY (categoria_id) REFERENCES Categorias(id),
    FOREIGN KEY (instructor_id) REFERENCES Usuarios(id)
);

CREATE TABLE IF NOT EXISTS Niveles (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único del nivel del curso',
    curso_id INT NOT NULL COMMENT 'ID del curso al que pertenece este nivel',
    nivel INT NOT NULL COMMENT 'Número de nivel dentro del curso',
    url_video VARCHAR(255) NOT NULL COMMENT 'URL o ruta del video',
    descripcion TEXT COMMENT 'Descripción del contenido de este nivel',
    estado INT DEFAULT 1 COMMENT 'Estado del nivel del curso en el portal (1: Activo, 0: Dado de baja)', 
    
    FOREIGN KEY (curso_id) REFERENCES Cursos(id)
);

------------------------------------------------------------------------------------------------------
-- SP gestion de usuarios
------------------------------------------------------------------------------------------------------
DELIMITER //

CREATE PROCEDURE InsertarUsuario(
    IN p_correo VARCHAR(100),
    IN p_contrasena VARCHAR(100),
    IN p_nombre VARCHAR(50),
    IN p_rol VARCHAR(20),
    IN p_genero VARCHAR(10),
    IN p_fecha_nacimiento DATE
)
BEGIN
    INSERT INTO usuarios (correo, contrasena, nombre, rol, genero, fecha_nacimiento)
    VALUES (p_correo, p_contrasena, p_nombre, p_rol, p_genero, p_fecha_nacimiento);
END //

DELIMITER ;

-- call insertarusuario('sp@test.com', '12', 'TESTER', 'ESTUDIANTE', 'MASCULINO', '1999-01-01');

DELIMITER //

CREATE PROCEDURE eliminar_usuario(IN p_id INT)
BEGIN
    UPDATE usuarios
    SET estado = 0
    WHERE id = p_id;
END //

DELIMITER ;

-- call eliminar_usuario(1);

DELIMITER //

CREATE PROCEDURE buscar_usuario(IN p_id INT)
BEGIN
    SELECT * FROM usuarios WHERE id = p_id;
END //

DELIMITER ;

-- CALL buscar_usuario(1);

DELIMITER //

CREATE PROCEDURE editar_usuario(
   
    IN p_correo VARCHAR(255),
    IN p_contrasena VARCHAR(255),
    IN p_nombre VARCHAR(100),
	IN p_id INT
)
BEGIN
    UPDATE usuarios
    SET correo = p_correo,
        contrasena = p_contrasena,
        nombre = p_nombre
    WHERE id = p_id;
END //

DELIMITER ;

-- CALL editar_usuario('nuevo_correo@example.com', '12', 'Nuevo Nombre', 1);

DELIMITER //

CREATE PROCEDURE login_usuario(IN p_correo VARCHAR(255))
BEGIN
    SELECT * FROM usuarios WHERE correo = p_correo and estado = 1;
END //

DELIMITER ;

-- CALL login_usuario('sarahi@test.com');  -- Cambia 'usuario@example.com' por el correo que deseas buscar
drop procedure login_usuario;

------------------------------------------------------------------------------------------------------
-- SP gestion de usuarios
------------------------------------------------------------------------------------------------------
