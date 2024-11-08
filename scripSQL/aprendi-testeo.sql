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
INSERT INTO Categorias (nombre, descripcion, usuario_creador_id, fecha_creacion, estado) 
VALUES 
('IT & Software', 'Categoria enfoicada a la tecnología', 1, '2024-10-21 22:04:14', 1),
('Marketing Digital', 'Categoria enfoicada en la publicidad', 1, '2024-10-21 22:04:14', 1),
('Desing Digital', 'Categoria enfoicada en el diseño digital', 1, '2024-10-21 22:04:14', 1),
('Craft', 'Categoria enfoicada en las manualidades', 1, '2024-10-21 22:04:14', 1);

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


------------------------------------------------------------------------------------------------------
-- cambios 05-11-2024 Inicio
------------------------------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS Inscripciones (
    curso_id INT NOT NULL COMMENT 'ID del curso al que se ha inscrito el estudiante',
    estudiante_id INT NOT NULL COMMENT 'ID del estudiante que se ha inscrito',
    fecha_inscripcion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que el estudiante se inscribió',
    fecha_ultima DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la ultima vez que entro al curso',
    fecha_terminacion DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de la terminacion del curso',
    progreso_curso DECIMAL(3,2) DEFAULT 0.00 COMMENT 'Progreso total del curso',
    precio_pagado DECIMAL(10,2) COMMENT 'Precio total pagado por el curso',
    tipo_pago VARCHAR(30) NOT NULL COMMENT 'Tipo de pago recibido por el curso',
    
    PRIMARY KEY (curso_id,estudiante_id),
    FOREIGN KEY (curso_id) REFERENCES Cursos(id),
    FOREIGN KEY (estudiante_id) REFERENCES Usuarios(id)	
);

drop table Niveles;
CREATE TABLE IF NOT EXISTS Niveles (
    curso_id INT NOT NULL COMMENT 'ID del curso al que pertenece este nivel',
    nivel INT NOT NULL COMMENT 'Número de nivel dentro del curso',
    url_video VARCHAR(255) NOT NULL COMMENT 'URL o ruta del video',
    descripcion TEXT COMMENT 'Descripción del contenido de este nivel',
    estado INT DEFAULT 1 COMMENT 'Estado del nivel del curso en el portal (1: Activo, 0: Dado de baja)', 
    
    primary key (curso_id, nivel),
    FOREIGN KEY (curso_id) REFERENCES Cursos(id)
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

DELIMITER //

CREATE PROCEDURE InsertarCertificado (
    IN p_estudiante_id INT,
    IN p_curso_id INT
)
BEGIN
    DECLARE v_nombre_estudiante VARCHAR(255);
    DECLARE v_nombre_curso VARCHAR(255);
    DECLARE v_nombre_instructor VARCHAR(255);

    -- Obtener el nombre del estudiante
    SELECT nombre INTO v_nombre_estudiante
    FROM Usuarios
    WHERE id = p_estudiante_id;

    -- Obtener el título del curso
    SELECT titulo INTO v_nombre_curso
    FROM Cursos
    WHERE id = p_curso_id;

    -- Obtener el nombre del instructor
    SELECT u.nombre INTO v_nombre_instructor
    FROM Cursos c
    JOIN Usuarios u ON c.instructor_id = u.id
    WHERE c.id = p_curso_id;

    -- Insertar el certificado
    INSERT INTO Certificados (estudiante_id, curso_id, nombre_estudiante, nombre_curso, nombre_instructor) 
    VALUES (p_estudiante_id, p_curso_id, v_nombre_estudiante, v_nombre_curso, v_nombre_instructor);
END //

DELIMITER ;

CREATE TABLE IF NOT EXISTS estudiantes_niveles(
	curso_id INT,
    nivel_id INT,
    estudiante_id INT,
    
    primary key(curso_id, nivel_id, estudiante_id),
	FOREIGN KEY (estudiante_id) REFERENCES Usuarios(id),
    FOREIGN KEY (curso_id, nivel_id) REFERENCES Niveles(curso_id, nivel)
);

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

------------------------------------------------------------------------------------------------------
-- cambios 05-11-2024 Final
------------------------------------------------------------------------------------------------------

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

select * from vista_cursos;
select * from comentarios;

CREATE TABLE IF NOT EXISTS Mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único del mensaje',	
    remitente_id INT NOT NULL COMMENT 'ID del usuario que envía el mensaje',
    destinatario_id INT NOT NULL COMMENT 'ID del usuario que recibe el mensaje',
    curso_id INT NOT NULL COMMENT 'ID del curso donde se origino el mensaje',
    contenido TEXT NOT NULL COMMENT 'Contenido del mensaje',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora en que se envió el mensaje',
	estado INT DEFAULT 1 COMMENT 'Estado del mensaje (1: Activo, 0: Borrado)',
    
    FOREIGN KEY (remitente_id) REFERENCES Usuarios(id),
    FOREIGN KEY (destinatario_id) REFERENCES Usuarios(id)
);
 
select * from mensajes where curso_id = 1 and (destinatario_id =1 or remitente_id=1) order by fecha asc;
select * from inscripciones;

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
        c.instructor_id = instructor_id_param   -- Filtramos por el instructor_id pasado como parámetro
    GROUP BY 
        c.id, c.titulo
    ORDER BY 
        total_estudiantes DESC;
END //

DELIMITER ;

call sp_lista_cursos_reporte (2);

DELIMITER //

CREATE PROCEDURE sp_kardex_estudiantes(IN estudiante_id_param INT)
BEGIN
    SELECT 
        c.titulo AS curso_titulo,
        ca.nombre AS categoria_nombre,  -- Aquí incluimos el campo 'nombre' de la tabla Categorias
        vi.progreso_curso,
        vi.fecha_inscripcion,
        vi.fecha_terminacion,
        vi.fecha_ultima
    FROM 
        vista_inscripciones vi
    JOIN 
        Cursos c ON vi.curso_id = c.id
    JOIN 
        Categorias ca ON c.categoria_id = ca.id  -- Aquí hacemos el JOIN con Categorias para obtener 'nombre'
    WHERE 
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

------------------------------------------------------------------------------------------------------
-- cambios 08-11-2024 Final
------------------------------------------------------------------------------------------------------