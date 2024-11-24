USE APRENDIV1;

-- sp usuarios
DELIMITER //

CREATE PROCEDURE sp_update_usuario_estado (
    IN p_id INT,
    IN p_estado INT
)
BEGIN
    UPDATE Usuarios
    SET estado = p_estado
    WHERE id = p_id;
END //

DELIMITER ;

CALL sp_update_usuario_estado(1, 1)

DELIMITER //

CREATE PROCEDURE sp_select_usuarios()
BEGIN
    SELECT * FROM Usuarios;
END //

DELIMITER ;

CALL sp_select_usuarios();

-- sp categorias

DELIMITER //

CREATE PROCEDURE editar_categorias(
    IN p_id INT,
    IN p_nombre VARCHAR(255),
    IN p_descripcion TEXT,
    IN p_createdBy INT
)
BEGIN
    UPDATE categorias 
    SET 
        nombre = p_nombre, 
        descripcion = p_descripcion, 
        usuario_creador_id = p_createdBy
    WHERE id = p_id;
END //

DELIMITER ;

CALL editar_categorias(3, 'Nuevo Nombre', 'Nueva Descripción', 2);

DELIMITER //

CREATE PROCEDURE eliminar_categoria(
    IN p_id INT
)
BEGIN
    UPDATE categorias 
    SET estado = 0
    WHERE id = p_id;
END //

DELIMITER ;

CALL eliminar_categoria(1);

DELIMITER //

CREATE PROCEDURE buscar_categoria_by_id(
    IN p_id INT
)
BEGIN
    SELECT * 
    FROM categorias
    WHERE id = p_id;
END //

DELIMITER ;

CALL buscar_categoria_by_id (3);
call insertarcategoria ('categoria test 1', 'categoria desc', 1);

DELIMITER //

CREATE PROCEDURE buscar_all_categorias()
BEGIN
    SELECT * 
    FROM categorias
    WHERE estado = 1;
END //

DELIMITER ;

CALL buscar_all_categorias();

-- sp cursos

DELIMITER //

CREATE PROCEDURE buscar_cursos_instructor(
    IN p_instructor_id INT,
    IN p_pagina INT
)
BEGIN
    SELECT * 
    FROM cursos
    WHERE instructor_id = p_instructor_id
    ORDER BY fecha_creacion DESC
    LIMIT 20 OFFSET p_pagina;
END //

DELIMITER ;

CALL buscar_cursos_instructor(2, 0);

DELIMITER //

CREATE PROCEDURE buscar_por_categoria(
    IN p_categoria_id INT,
    IN p_pagina INT
)
BEGIN
    SELECT * 
    FROM cursos
    WHERE estado = 1 
      AND categoria_id = p_categoria_id
    ORDER BY fecha_creacion DESC
    LIMIT 20 OFFSET p_pagina;
END //

CALL buscar_por_categoria(2, 0);

DELIMITER //

CREATE PROCEDURE buscar_all_cursos(
    IN p_pagina INT
)
BEGIN
    SELECT * 
    FROM cursos
    WHERE estado = 1
    ORDER BY fecha_creacion DESC
    LIMIT 20 OFFSET p_pagina;
END //

DELIMITER ;

CALL buscar_all_cursos(0);

DELIMITER //

-- existe?
CREATE PROCEDURE buscar_curso_id(
    IN p_id INT
)
BEGIN
    SELECT * 
    FROM cursos
    WHERE id = p_id 
      AND estado = 1;
END //

DELIMITER ;

CALL buscar_curso_id(2);

DELIMITER //

CREATE PROCEDURE eliminar_curso(
    IN p_id INT
)
BEGIN
    UPDATE cursos 
    SET estado = 0
    WHERE id = p_id;
END //

DELIMITER ;

CALL eliminar_curso(2);

DELIMITER //

CREATE PROCEDURE registrar_curso(
    IN p_titulo VARCHAR(255),
    IN p_descripcion TEXT,
    IN p_costo DECIMAL(10, 2),
    IN p_instructor INT,
    IN p_categoria INT,
    IN p_imagen MEDIUMBLOB
)
BEGIN
    INSERT INTO cursos (titulo, descripcion, costo, instructor_id, categoria_id, imagen)
    VALUES (p_titulo, p_descripcion, p_costo, p_instructor, p_categoria, p_imagen);
END //

DELIMITER ;

DELIMITER //

CREATE PROCEDURE editar_curso(
    IN p_id INT,
    IN p_titulo VARCHAR(255),
    IN p_descripcion TEXT,
    IN p_costo DECIMAL(10, 2),
    IN p_categoria INT,
	IN p_imagen MEDIUMBLOB
)
BEGIN
    UPDATE cursos
    SET 
        titulo = p_titulo, 
        descripcion = p_descripcion, 
        costo = p_costo, 
        categoria_id = p_categoria,
        imagen = p_imagen
        
    WHERE id = p_id;
END //

DELIMITER ;	

-- niveles 

DELIMITER //

CREATE PROCEDURE buscar_all_niveles(
    IN p_curso_id INT
)
BEGIN
    SELECT * 
    FROM niveles
    WHERE estado = 1
      AND curso_id = p_curso_id;
END //

DELIMITER ;

CALL buscar_all_niveles(1);

drop procedure buscar_nivel_id;
DELIMITER //

CREATE PROCEDURE buscar_nivel_id(
    IN p_curso_id INT,
    IN p_nivel INT
)
BEGIN
    SELECT * 
    FROM niveles
    WHERE estado = 1
      AND curso_id = p_curso_id
      AND nivel = p_nivel;
END //

DELIMITER ;

CALL buscar_nivel_id(1, 2);

DELIMITER //

CREATE PROCEDURE eliminar_nivel(
    IN p_curso_id INT,
    IN p_nivel INT
)
BEGIN
    UPDATE niveles
    SET estado = 0
    WHERE curso_id = p_curso_id
      AND nivel = p_nivel;
END //

DELIMITER ;


CALL eliminar_nivel(1, 2);

DELIMITER //

CREATE PROCEDURE registrarNivel(
    IN p_curso_id INT,
    IN p_nivel INT,
    IN p_url_video VARCHAR(255),
    IN p_descripcion TEXT
)
BEGIN
    INSERT INTO niveles (curso_id, nivel, url_video, descripcion)
    VALUES (p_curso_id, p_nivel, p_url_video, p_descripcion);
END //

DELIMITER ;

CALL registrarNivel(2, 3, 'http://video.com/curso2', 'Descripción del nivel 2');
select * from niveles;

DELIMITER //

CREATE PROCEDURE editar_nivel(
    IN p_curso_id INT,
    IN p_nivel INT,
    IN p_descripcion TEXT,
    IN p_url_video VARCHAR(255)
)
BEGIN
    UPDATE niveles
    SET 
        descripcion = p_descripcion, 
        url_video = p_url_video
    WHERE curso_id = p_curso_id 
      AND nivel = p_nivel;
END //

DELIMITER ;

CALL editar_nivel(2, 3, 'Nueva descripción del nivel 2', 'http://video.com/nuevo_url');

-- mensajes

DELIMITER //

CREATE PROCEDURE registrar_mensajes(
    IN p_remitente_id INT,
    IN p_destinatario_id INT,
    IN p_curso_id INT,
    IN p_contenido TEXT
)
BEGIN
    INSERT INTO mensajes (remitente_id, destinatario_id, curso_id, contenido)
    VALUES (p_remitente_id, p_destinatario_id, p_curso_id, p_contenido);
END //

DELIMITER ;

CALL registrar_mensajes(1, 2, 1, 'Este es el contenido del mensaje');
select * from mensajes;


-- certificados



DELIMITER //

CREATE PROCEDURE buscar_certificado_id(
    IN p_estudiante_id INT,
    IN p_curso_id INT
)
BEGIN
    SELECT * 
    FROM certificados
    WHERE estudiante_id = p_estudiante_id AND curso_id = p_curso_id;
END //

DELIMITER ;

CALL buscar_certificado_id(1, 1);

DELIMITER //

CREATE PROCEDURE buscar_all_certificados(
    IN p_id INT
)
BEGIN
    SELECT * 
    FROM certificados
    WHERE estudiante_id = p_id;
END //

DELIMITER ;

CALL buscar_all_certificados(1);

select * from certificados

-- estudiantes niveles

DELIMITER //

CREATE PROCEDURE buscar_estudiante_niveles(
    IN p_curso_id INT,
    IN p_estudiante_id INT
)
BEGIN
    SELECT * 
    FROM estudiantes_niveles
    WHERE curso_id = p_curso_id AND estudiante_id = p_estudiante_id;
END //

DELIMITER ;

CALL buscar_estudiante_niveles(1, 1);

DELIMITER //

CREATE PROCEDURE insertar_nivel_estudiante(
    IN p_curso_id INT,
    IN p_nivel_id INT,
    IN p_estudiante_id INT
)
BEGIN
    INSERT INTO estudiantes_niveles (curso_id, nivel_id, estudiante_id)
    VALUES (p_curso_id, p_nivel_id, p_estudiante_id);
END //

DELIMITER ;

CALL insertar_nivel_estudiante(1, 2, 1);

-- reportes

DELIMITER //

CREATE PROCEDURE obtener_reporte_adminInstructores()
BEGIN
    SELECT * 
    FROM reporteAdminInstructores;
END //

DELIMITER ;

CALL obtener_reporte_adminInstructores();

-- inscripciones

DELIMITER //

CREATE PROCEDURE buscar_Inscripciones_Idcurso(
    IN p_curso_id INT
)
BEGIN
    SELECT * 
    FROM vista_inscripciones
    WHERE curso_id = p_curso_id;
END //

DELIMITER ;

CALL buscar_Inscripciones_Idcurso(1);

DELIMITER //

CREATE PROCEDURE buscar_all_inscripciones(
    IN p_estudiante_id INT
)
BEGIN
    SELECT * 
    FROM vista_inscripciones
    WHERE estudiante_id = p_estudiante_id;
END //

DELIMITER ;

CALL buscar_all_inscripciones(1);

DELIMITER //

CREATE PROCEDURE buscar_inscripcion_id(
    IN p_curso_id INT,
    IN p_estudiante_id INT
)
BEGIN
    SELECT * 
    FROM vista_inscripciones
    WHERE curso_id = p_curso_id AND estudiante_id = p_estudiante_id;
END //

DELIMITER ;


CALL buscar_inscripcion_id(1, 1);