    CREATE DATABASE IF NOT EXISTS APRENDI;
    USE APRENDI;

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

    -- falta validad con las apis --

    CREATE TABLE IF NOT EXISTS Niveles (
        id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único del nivel del curso',
        curso_id INT NOT NULL COMMENT 'ID del curso al que pertenece este nivel',
        nivel INT NOT NULL COMMENT 'Número de nivel dentro del curso',
        url_video VARCHAR(255) NOT NULL COMMENT 'URL o ruta del video',
        descripcion TEXT COMMENT 'Descripción del contenido de este nivel',
        estado INT DEFAULT 1 COMMENT 'Estado del nivel del curso en el portal (1: Activo, 0: Dado de baja)', 
        
        FOREIGN KEY (curso_id) REFERENCES Cursos(id)
    );

    CREATE TABLE IF NOT EXISTS Inscripciones (
        id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único de la inscripción',
        curso_id INT NOT NULL COMMENT 'ID del curso al que se ha inscrito el estudiante',
        estudiante_id INT NOT NULL COMMENT 'ID del estudiante que se ha inscrito',
        fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que el estudiante se inscribió',
        fecha_ultima TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la ultima vez que entro al curso',
        fecha_terminacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la terminacion del curso',
        progreso_curso INT DEFAULT 0 COMMENT 'Progreso total del curso',
        estado CHAR(1) DEFAULT 'P' COMMENT 'Estado de la inscripción del estudiante en el curso (P: En progreso, C: Completo)',
        precio_pagado DECIMAL(10,2) COMMENT 'Precio total pagado por el curso',
        
        FOREIGN KEY (curso_id) REFERENCES Cursos(id),
        FOREIGN KEY (estudiante_id) REFERENCES Usuarios(id)
    );

    CREATE TABLE IF NOT EXISTS Certificados (
        id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único del certificado',
        estudiante_id INT NOT NULL COMMENT 'ID del estudiante que recibe el certificado',
        curso_id INT NOT NULL COMMENT 'ID del curso que ha completado el estudiante',
        fecha_emision DATE NOT NULL COMMENT 'Fecha de emisión del certificado',
        nombre_estudiante VARCHAR(100) NOT NULL COMMENT 'Nombre completo del estudiante',
        nombre_curso VARCHAR(255) NOT NULL COMMENT 'Nombre del curso completado',
        nombre_instructor VARCHAR(100) NOT NULL COMMENT 'Nombre del instructor que certifica el curso',
        
        FOREIGN KEY (estudiante_id) REFERENCES Usuarios(id),
        FOREIGN KEY (curso_id) REFERENCES Cursos(id)
    );

    CREATE TABLE IF NOT EXISTS Mensajes (
        id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único del mensaje',
        remitente_id INT NOT NULL COMMENT 'ID del usuario que envía el mensaje',
        destinatario_id INT NOT NULL COMMENT 'ID del usuario que recibe el mensaje',
        contenido TEXT NOT NULL COMMENT 'Contenido del mensaje',
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que se envió el mensaje',
        estado INT DEFAULT 1 COMMENT 'Estado del mensaje (1: Activo, 0: Borrado)',
        
        FOREIGN KEY (remitente_id) REFERENCES Usuarios(id),
        FOREIGN KEY (destinatario_id) REFERENCES Usuarios(id)
    );

    CREATE TABLE IF NOT EXISTS Comentarios (
        id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único del comentario',
        curso_id INT NOT NULL COMMENT 'ID del curso al que se refiere el comentario',
        usuario_id INT NOT NULL COMMENT 'ID del usuario que hizo el comentario',
        contenido TEXT NOT NULL COMMENT 'Contenido del comentario',
        calificacion INT COMMENT 'Calificación dada al curso (1 al 5)',
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que se hizo el comentario',
        estado INT DEFAULT 1 COMMENT 'Estado del usuario en el portal (1: Activo, 0: Borrado, 2:Bloqueado)',
        
        FOREIGN KEY (curso_id) REFERENCES Cursos(id),
        FOREIGN KEY (usuario_id) REFERENCES Usuarios(id)
    );

    CREATE TABLE IF NOT EXISTS Pagos (
        id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único del pago',
        curso_id INT NOT NULL COMMENT 'ID del curso por el cual se realizó el pago',
        estudiante_id INT NOT NULL COMMENT 'ID del estudiante que realizó el pago',
        monto DECIMAL(10, 2) NOT NULL COMMENT 'Monto del pago realizado',
        forma_pago CHAR(1) NOT NULL COMMENT 'Forma de pago utilizada (T: Tarjeta, P: PayPal, X: Transferencia)',
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que se realizó el pago',
        
        FOREIGN KEY (curso_id) REFERENCES Cursos(id),
        FOREIGN KEY (estudiante_id) REFERENCES Usuarios(id)
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

DELIMITER //

CREATE PROCEDURE eliminar_usuario(IN p_id INT)
BEGIN
    UPDATE usuarios
    SET estado = 0
    WHERE id = p_id;
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE buscar_usuario(IN p_id INT)
BEGIN
    SELECT * FROM usuarios WHERE id = p_id;
END //

DELIMITER ;

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

DELIMITER //

CREATE PROCEDURE login_usuario(IN p_correo VARCHAR(255))
BEGIN
    SELECT * FROM usuarios WHERE correo = p_correo and estado = 1;
END //

DELIMITER ;

drop procedure login_usuario;

------------------------------------------------------------------------------------------------------
-- SP gestion de usuarios
------------------------------------------------------------------------------------------------------
