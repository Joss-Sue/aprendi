Use APRENDI;

DELIMITER //

DROP PROCEDURE IF EXISTS sp_insertar_usuario //
DROP PROCEDURE IF EXISTS sp_insertar_curso //
DROP PROCEDURE IF EXISTS sp_insertar_estudiante_nivel //
DROP PROCEDURE IF EXISTS sp_actualizar_curso //
DROP PROCEDURE IF EXISTS sp_consultar_usuario //
DROP PROCEDURE IF EXISTS sp_ventas_por_curso //
DROP PROCEDURE IF EXISTS sp_kardex_estudiantes //

CREATE PROCEDURE sp_insertar_usuario( -- Insertar usuario
    IN p_nombre VARCHAR(100),
    IN p_correo VARCHAR(150),
    IN p_contrasena VARCHAR(100),
    IN p_tipo ENUM('instructor', 'estudiante')
)
BEGIN
    INSERT INTO Usuarios (nombre, correo, contrasena, tipo)
    VALUES (p_nombre, p_correo, p_contrasena, p_tipo);
END //

CREATE PROCEDURE sp_insertar_curso( -- Crear procedimiento para insertar un curso
    IN p_titulo VARCHAR(255),
    IN p_categoria VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_instructor_id INT,
    IN p_precio DECIMAL(10, 2)
)
BEGIN
    INSERT INTO Cursos (titulo, categoria, descripcion, instructor_id, precio)
    VALUES (p_titulo, p_categoria, p_descripcion, p_instructor_id, p_precio);
END //


CREATE PROCEDURE sp_insertar_estudiante_nivel( -- Insertar un nivel del estudiante
    IN p_estudiante_id INT,
    IN p_curso_id INT,
    IN p_nivel_actual INT
)
BEGIN
    INSERT INTO Estudiantes_Niveles (estudiante_id, curso_id, nivel_actual)
    VALUES (p_estudiante_id, p_curso_id, p_nivel_actual);
END //


CREATE PROCEDURE sp_actualizar_curso( -- Actualizar un curso
    IN p_curso_id INT,
    IN p_titulo VARCHAR(255),
    IN p_categoria VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_precio DECIMAL(10, 2)
)
BEGIN
    UPDATE Cursos
    SET titulo = p_titulo,
        categoria = p_categoria,
        descripcion = p_descripcion,
        precio = p_precio
    WHERE curso_id = p_curso_id;
END //

CREATE PROCEDURE sp_consultar_usuario( -- Consultar un usuario por ID
    IN p_usuario_id INT
)
BEGIN
    SELECT *
    FROM Usuarios
    WHERE usuario_id = p_usuario_id;
END //

CREATE PROCEDURE sp_ventas_por_curso() -- Consultar ventas por curso
BEGIN
    SELECT c.titulo, 
           COUNT(e.curso_id) AS inscritos, 
           SUM(c.precio) AS ingresos_totales
    FROM Cursos c
    LEFT JOIN Estudiantes_Niveles e ON c.curso_id = e.curso_id
    GROUP BY c.curso_id;
END //

CREATE PROCEDURE sp_kardex_estudiantes( -- Consultar el progreso (kardex) de estudiantes
    IN estudiante_id_param INT
)
BEGIN
    SELECT c.titulo,
           c.categoria,
           e.nivel_actual,
           (e.nivel_actual * 100) / c.total_niveles AS progreso
    FROM Estudiantes_Niveles e
    INNER JOIN Cursos c ON e.curso_id = c.curso_id
    WHERE e.estudiante_id = estudiante_id_param;
END //

DELIMITER ;