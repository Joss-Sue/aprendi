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

        FOREIGN KEY (categoria_id) REFERENCES Categorias(id),
        FOREIGN KEY (instructor_id) REFERENCES Usuarios(id)
    );

    -- falta validad con las apis --

    CREATE TABLE IF NOT EXISTS Niveles (
        curso_id INT NOT NULL COMMENT 'ID del curso al que pertenece este nivel',
        nivel INT NOT NULL COMMENT 'Número de nivel dentro del curso',
        url_video VARCHAR(255) NOT NULL COMMENT 'URL o ruta del video',
        descripcion TEXT COMMENT 'Descripción del contenido de este nivel',
        estado INT DEFAULT 1 COMMENT 'Estado del nivel del curso en el portal (1: Activo, 0: Dado de baja)', 
        
        PRIMARY KEY (curso_id, nivel),
        FOREIGN KEY (curso_id) REFERENCES Cursos(id)
);

    CREATE TABLE IF NOT EXISTS Inscripciones (
        curso_id INT NOT NULL COMMENT 'ID del curso al que se ha inscrito el estudiante',
        estudiante_id INT NOT NULL COMMENT 'ID del estudiante que se ha inscrito',
        fecha_inscripcion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que el estudiante se inscribió',
        fecha_ultima DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la ultima vez que entro al curso',
        fecha_terminacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la terminacion del curso',
        progreso_curso DECIMAL(3,2) DEFAULT 0.00 COMMENT 'Progreso total del curso',
        precio_pagado DECIMAL(10,2) COMMENT 'Precio total pagado por el curso',
        tipo_pago VARCHAR(30) NOT NULL COMMENT 'Tipo de pago recibido por el curso',
        curso_terminado INT default 0,
        
        PRIMARY KEY (curso_id,estudiante_id),
        FOREIGN KEY (curso_id) REFERENCES Cursos(id),
        FOREIGN KEY (estudiante_id) REFERENCES Usuarios(id)	
);

    CREATE TABLE IF NOT EXISTS Certificados (
        estudiante_id INT NOT NULL COMMENT 'ID del estudiante que recibe el certificado',
        curso_id INT NOT NULL COMMENT 'ID del curso que ha completado el estudiante',
        fecha_emision DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de emisión del certificado',
        nombre_estudiante VARCHAR(100) COMMENT 'Nombre completo del estudiante',
        nombre_curso VARCHAR(255) COMMENT 'Nombre del curso completado',
        nombre_instructor VARCHAR(100) COMMENT 'Nombre del instructor que certifica el curso',
        
        PRIMARY KEY (estudiante_id,curso_id),
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

    CREATE TABLE IF NOT EXISTS estudiantes_niveles(
        curso_id INT,
        nivel_id INT,
        estudiante_id INT,
        
        primary key(curso_id, nivel_id, estudiante_id),
        FOREIGN KEY (estudiante_id) REFERENCES Usuarios(id),
        FOREIGN KEY (curso_id, nivel_id) REFERENCES Niveles(curso_id, nivel)
    );

------------------------------------------------------------------------------------------------------
-- Vistas 5/8
------------------------------------------------------------------------------------------------------

CREATE VIEW vista_inscripciones AS
    SELECT 
        i.curso_id,
        i.estudiante_id,
        i.fecha_inscripcion,
        i.fecha_ultima,
        i.fecha_terminacion,
        i.precio_pagado,
        i.tipo_pago,
        COALESCE(
            (COUNT(en.curso_id) / n.total_niveles) * 100, 
            0
        ) AS progreso_curso
    FROM 
        Inscripciones i
    LEFT JOIN 
        estudiantes_niveles en ON i.curso_id = en.curso_id AND i.estudiante_id = en.estudiante_id
    JOIN 
        (
            SELECT curso_id, COUNT(*) AS total_niveles
            FROM Niveles
            GROUP BY curso_id
        ) n ON i.curso_id = n.curso_id
    GROUP BY 
        i.curso_id, i.estudiante_id;


CREATE VIEW vista_cursos AS
    SELECT 
        c.*, 
        COALESCE(AVG(com.calificacion), 0) AS promedio_calificacion
    FROM 
        cursos c
    LEFT JOIN 
        comentarios com ON c.id = com.curso_id
    GROUP BY 
        c.id;

    CREATE VIEW reporteAdminInstructores
    AS
    SELECT 
        u.correo AS usuario,
        u.nombre,
        u.fecha_registro AS fecha_ingreso,
        COUNT(c.id) AS cantidad_cursos_ofrecidos,
        COALESCE(SUM(i.precio_pagado), 0) AS total_ganancias
    FROM 
        Usuarios u
    LEFT JOIN Cursos c ON u.id = c.instructor_id
    LEFT JOIN Inscripciones i ON c.id = i.curso_id
    WHERE 
        u.rol = 'INSTRUCTOR'
        AND u.estado = 1
    GROUP BY 
        u.id;


CREATE VIEW cursos_mas_vendidos
    AS
    SELECT 
        c.id AS curso_id,
        c.titulo,
        c.descripcion,
        c.costo,
        c.fecha_creacion,
        COUNT(i.estudiante_id) AS total_inscripciones
    FROM 
        Cursos c
    LEFT JOIN 
        Inscripciones i ON c.id = i.curso_id
    WHERE 
        c.estado = 1 
    GROUP BY 
        c.id
    ORDER BY 
        total_inscripciones DESC
    LIMIT 5; 


CREATE VIEW cursos_mas_recientes
    AS
    SELECT 
        id AS curso_id,
        titulo,
        descripcion,
        costo,
        fecha_creacion
    FROM 
        Cursos
    WHERE 
        estado = 1
    ORDER BY 
        fecha_creacion DESC 
    LIMIT 5;


CREATE VIEW cursos_mejor_calificados
    AS
    SELECT 
        c.id AS curso_id,
        c.titulo,
        c.descripcion,
        c.costo,
        COALESCE(AVG(com.calificacion), 0) AS promedio_calificacion
    FROM 
        Cursos c
    LEFT JOIN 
        Comentarios com ON c.id = com.curso_id AND com.estado = 1 
    WHERE 
        c.estado = 1 
    GROUP BY 
        c.id
    ORDER BY 
        promedio_calificacion DESC 
    LIMIT 5;
------------------------------------------------------------------------------------------------------
-- Vistas
------------------------------------------------------------------------------------------------------

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

------------------------------------------------------------------------------------------------------
-- SP ordenar y agregar sp por objeto
------------------------------------------------------------------------------------------------------

DELIMITER //

CREATE PROCEDURE InsertarCertificado (
    IN p_estudiante_id INT,
    IN p_curso_id INT
)
BEGIN
    DECLARE v_nombre_estudiante VARCHAR(255);
    DECLARE v_nombre_curso VARCHAR(255);
    DECLARE v_nombre_instructor VARCHAR(255);

    SELECT nombre INTO v_nombre_estudiante
    FROM Usuarios
    WHERE id = p_estudiante_id;

    SELECT titulo INTO v_nombre_curso
    FROM Cursos
    WHERE id = p_curso_id;

    SELECT u.nombre INTO v_nombre_instructor
    FROM Cursos c
    JOIN Usuarios u ON c.instructor_id = u.id
    WHERE c.id = p_curso_id;

    INSERT INTO Certificados (estudiante_id, curso_id, nombre_estudiante, nombre_curso, nombre_instructor) 
    VALUES (p_estudiante_id, p_curso_id, v_nombre_estudiante, v_nombre_curso, v_nombre_instructor);
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE sp_lista_cursos_reporte(IN instructor_id_param INT)
BEGIN
    SELECT 
        c.id AS curso_id,
        c.titulo AS curso_titulo,
        COUNT(i.estudiante_id) AS total_estudiantes,
        COALESCE(AVG(i.progreso_curso), 0) AS promedio_progreso_curso,
        COALESCE(AVG(i.precio_pagado), 0) AS promedio_precio_pagado
    FROM 
        Cursos c
    LEFT JOIN 
        vista_inscripciones i ON c.id = i.curso_id
    WHERE
        c.instructor_id = instructor_id_param  
    GROUP BY 
        c.id, c.titulo
    ORDER BY 
        total_estudiantes DESC;
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE sp_kardex_estudiantes(IN estudiante_id_param INT)
BEGIN
    SELECT 
        c.titulo AS curso_titulo,
        ca.nombre AS categoria_nombre, 
        vi.progreso_curso,
        vi.fecha_inscripcion,
        vi.fecha_terminacion,
        vi.fecha_ultima
    FROM 
        vista_inscripciones vi
    JOIN 
        Cursos c ON vi.curso_id = c.id
    JOIN 
        Categorias ca ON c.categoria_id = ca.id  
        vi.estudiante_id = estudiante_id_param;
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE sp_ventas_por_curso(IN curso_id_param INT)
BEGIN
    SELECT 
        u.nombre AS nombre_estudiante,
        c.titulo AS curso_titulo,
        vi.fecha_inscripcion,
        vi.progreso_curso,
        vi.precio_pagado,
        vi.tipo_pago
    FROM 
        vista_inscripciones vi
    JOIN 
        Cursos c ON vi.curso_id = c.id
    JOIN 
        Usuarios u ON vi.estudiante_id = u.id
    WHERE 
        vi.curso_id = curso_id_param;
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE sp_ventas_por_curso(IN curso_id_param INT)
BEGIN
    SELECT 
        u.nombre AS nombre_estudiante,
        c.titulo AS curso_titulo,
        vi.fecha_inscripcion,
        vi.progreso_curso,
        vi.precio_pagado,
        vi.tipo_pago
    FROM 
        vista_inscripciones vi
    JOIN 
        Cursos c ON vi.curso_id = c.id
    JOIN 
        Usuarios u ON vi.estudiante_id = u.id
    WHERE 
        vi.curso_id = curso_id_param;
END //

DELIMITER ;

DROP PROCEDURE IF EXISTS InsertarUsuario;
DELIMITER //

CREATE PROCEDURE InsertarUsuario(
    IN p_correo VARCHAR(100),
    IN p_contrasena VARCHAR(100),
    IN p_nombre VARCHAR(50),
    IN p_rol VARCHAR(20),
    IN p_genero VARCHAR(10),
    IN p_fecha_nacimiento DATE,
    IN p_foto MEDIUMBLOB
)
BEGIN
    INSERT INTO usuarios (correo, contrasena, nombre, rol, genero, fecha_nacimiento, foto)
    VALUES (p_correo, p_contrasena, p_nombre, p_rol, p_genero, p_fecha_nacimiento, p_foto);
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE InsertarCategoria(
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_usuario_creador_id INT
)
BEGIN
    INSERT INTO Categorias (nombre, descripcion, usuario_creador_id)
    VALUES (p_nombre, p_descripcion, p_usuario_creador_id);
    
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE BuscarCursos(
    IN p_valor_busqueda VARCHAR(255), 
    IN p_categoria_id INT,
	IN p_instructor_id INT,
    IN p_fecha_inicio DATE, 
    IN p_fecha_fin DATE 
)
BEGIN
	SELECT 
		c.id,
		c.titulo,
		c.descripcion,
		c.costo,
		c.estado,
		c.fecha_creacion,
		cat.nombre AS categoria,
		u.nombre AS instructor
	FROM Cursos c
	INNER JOIN Categorias cat ON c.categoria_id = cat.id
	INNER JOIN Usuarios u ON c.instructor_id = u.id
	WHERE (p_valor_busqueda = '' OR c.titulo LIKE CONCAT('%', p_valor_busqueda, '%'))
	AND c.estado = 1
	AND c.categoria_id = IF (p_categoria_id <> 0, p_categoria_id, c.categoria_id )
	AND c.instructor_id = IF (p_instructor_id <> 0, p_instructor_id, c.instructor_id )
	AND DATE(c.fecha_creacion) BETWEEN p_fecha_inicio AND p_fecha_fin;

END //

DELIMITER ;

    DELIMITER //

CREATE PROCEDURE ObtenerMensajesConUsuarios(
    IN p_curso_id INT,
    IN p_usuario_id INT
)
BEGIN
    SELECT 
        m.id AS mensaje_id,
        m.curso_id,
        m.contenido,
        m.fecha,
        remitente.id AS remitente_id,
        remitente.nombre AS remitente_nombre,
        destinatario.id AS destinatario_id,
        destinatario.nombre AS destinatario_nombre
    FROM 
        Mensajes m
    JOIN 
        Usuarios remitente ON m.remitente_id = remitente.id
    JOIN 
        Usuarios destinatario ON m.destinatario_id = destinatario.id
    WHERE 
        m.curso_id = p_curso_id 
        AND (m.destinatario_id = p_usuario_id OR m.remitente_id = p_usuario_id)	
    ORDER BY 
        m.fecha ASC;
END//

DELIMITER ;

DELIMITER //

CREATE PROCEDURE cursos_pantalla_principal(
    IN p_filtro INT
)
BEGIN
    IF p_filtro = 1 THEN
        SELECT * FROM cursos_mas_recientes;
    ELSEIF p_filtro = 2 THEN
        SELECT * FROM cursos_mejor_calificados;
    ELSEIF p_filtro = 3 THEN
        SELECT * FROM cursos_mas_vendidos;
    END IF;
END//

DELIMITER ;

------------------------------------------------------------------------------------------------------
-- SP ordenar y agregar sp por objeto
------------------------------------------------------------------------------------------------------