document.addEventListener('DOMContentLoaded', async function () {

    if (usuarioRol === "estudiante") {
        await cargarCursosInscritos(usuarioId);
    } else if (usuarioRol === "instructor") {
        await cargarEstudiantesCursos(usuarioId);
    }

    document.getElementById('mensajeriaForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        await enviarMensaje(usuarioId);
    });
    const botonesUsuarios = document.querySelectorAll('.list-group-item');
    botonesUsuarios.forEach(boton => {
        boton.addEventListener('click', () => {
            botonesUsuarios.forEach(b => b.classList.remove('seleccionado'));
            boton.classList.add('seleccionado');
        });
    });

    // Recargar mensajes cada 5 segundos
    setInterval(() => {
        if (cursoSeleccionado && usuarioSeleccionado) {
            cargarMensajes(cursoSeleccionado, usuarioId);
        }
    }, 5000);

});

let cursoSeleccionado = null;
let usuarioSeleccionado = null;

// Cargar estudiantes inscritos en los cursos registrados por el instructor
async function cargarEstudiantesCursos(usuarioId) {

    // Obtener todos los cursos usando cursoController.php (sin filtrar por ID)
    const urlCursos = `http://localhost/aprendi/api/cursoController.php?id=${usuarioId}&pagina=1`;

    try {
        const responseCursos = await fetch(urlCursos);

        if (!responseCursos.ok) {
            throw new Error("Error al obtener cursos");
        }

        const cursos = await responseCursos.json();

        if (!Array.isArray(cursos) || cursos.length === 0) {
            console.error("No se encontraron cursos");
            return;
        }

        const usuariosContainer = document.getElementById('usuariosContainer');
        usuariosContainer.innerHTML = '';

        // Iterar sobre cada curso del instructor y buscar estudiantes inscritos
        for (const curso of cursos) {
            const cursoId = curso.id;
            const cursoTitulo = curso.titulo || 'Curso Desconocido';

            // Obtener estudiantes inscritos en el curso usando inscripcionesController.php
            const urlEstudiantes = `http://localhost/aprendi/api/inscripcionesController.php?curso_id=${cursoId}`;

            const responseEstudiantes = await fetch(urlEstudiantes);

            if (!responseEstudiantes.ok) {
                console.error("Error al obtener estudiantes para el curso:", cursoId);
                continue;
            }

            const estudiantes = await responseEstudiantes.json();

            if (!Array.isArray(estudiantes) || estudiantes.length === 0) {
                console.log(`No hay estudiantes inscritos en el curso: ${cursoId}`);
                continue;
            }

            // Iterar sobre los estudiantes inscritos y mostrarlos en la interfaz
            for (const estudiante of estudiantes) {
                const estudianteId = estudiante.estudiante_id;
                const urlUsuario = `http://localhost/aprendi/api/usuariosController.php?id=${encodeURIComponent(estudianteId)}`;

                const responseUsuario = await fetch(urlUsuario);
                if (!responseUsuario.ok) {
                    console.error("Error al obtener el usuario con id:", estudianteId);
                    continue;
                }

                const usuario = await responseUsuario.json();

                // Crear un elemento para cada estudiante
                const usuarioElement = document.createElement('div');
                usuarioElement.classList.add('list-group-item');
                usuarioElement.textContent = `${usuario.nombre || usuario.correo} - ${cursoTitulo}`;
                usuarioElement.dataset.estudianteId = usuario.id;
                usuarioElement.dataset.cursoId = cursoId;

                // Asignar evento para cargar mensajes
                usuarioElement.addEventListener('click', () => {
                    cursoSeleccionado = cursoId;
                    usuarioSeleccionado = usuario.id;
                    cargarMensajes(cursoId, usuario.id);
                });

                usuariosContainer.appendChild(usuarioElement);
            }
        }
    } catch (error) {
        console.error("Error al cargar estudiantes:", error);
    }
}
// Cargar los cursos en los que el estudiante está inscrito
async function cargarCursosInscritos(usuarioId) {

    // Obtener todos los cursos en los que el estudiante está inscrito usando inscripcionesController.php
    const urlInscripciones = `http://localhost/aprendi/api/inscripcionesController.php?id=${usuarioId}`;

    try {
        const responseInscripciones = await fetch(urlInscripciones);

        if (!responseInscripciones.ok) {
            throw new Error("Error al obtener los cursos inscritos");
        }

        const inscripciones = await responseInscripciones.json();

        if (!Array.isArray(inscripciones) || inscripciones.length === 0) {
            console.error("No se encontraron cursos inscritos");
            return;
        }

        const usuariosContainer = document.getElementById('usuariosContainer');
        usuariosContainer.innerHTML = '';

        // Iterar sobre cada inscripción del estudiante para mostrar los cursos en los que está inscrito
        for (const inscripcion of inscripciones) {
            const cursoId = inscripcion.curso_id;

            // Obtener detalles del curso usando cursoController.php
            const urlCurso = `http://localhost/aprendi/api/cursoController.php?id=${encodeURIComponent(cursoId)}`;

            const responseCurso = await fetch(urlCurso);
            if (!responseCurso.ok) {
                console.error("Error al obtener detalles del curso con id:", cursoId);
                continue;
            }

            const curso = await responseCurso.json();
            const cursoTitulo = curso.titulo || 'Curso Desconocido';

            // Crear un elemento para cada curso en el que el estudiante está inscrito
            const cursoElement = document.createElement('div');
            cursoElement.classList.add('list-group-item');
            cursoElement.textContent = `${cursoTitulo}`;
            cursoElement.dataset.cursoId = cursoId;

            // Asignar evento para cargar mensajes del curso
            cursoElement.addEventListener('click', () => {
                cursoSeleccionado = cursoId;
                usuarioSeleccionado = usuarioId; // El estudiante quiere ver mensajes del curso en el que está inscrito
                cargarMensajes(cursoId, usuarioId);
            });

            usuariosContainer.appendChild(cursoElement);
        }
    } catch (error) {
        console.error("Error al cargar cursos inscritos:", error);
    }
}

// Obtener detalles del curso
async function obtenerDetallesCurso(cursoId) {
    const url = `http://localhost/aprendi/api/cursoController.php?id=${encodeURIComponent(cursoId)}`;
    const response = await fetch(url);
    return await response.json();
}

// Enviar mensaje
async function enviarMensaje(remitenteId) {
    const mensajeText = document.getElementById('mensaje').value;

    if (!cursoSeleccionado || !usuarioSeleccionado || !mensajeText) {
        mensajeError.textContent = 'Por favor, selecciona un usuario y escribe un mensaje.';
        return;
    }
    // Limpiar mensaje de error si está todo correcto
    mensajeError.textContent = '';

    let destinatarioId;

    // Determina el destinatario en función del rol del remitente
    if (usuarioRol === "estudiante") {
        // Si es un estudiante, el destinatario es el instructor del curso
        destinatarioId = await obtenerInstructorCurso(cursoSeleccionado);
    } else if (usuarioRol === "instructor") {
        // Si es un instructor, el destinatario es el estudiante seleccionado
        destinatarioId = usuarioSeleccionado;
    }

    if (!destinatarioId) {
        console.error('No se pudo determinar el destinatario del mensaje.');
        return;
    }

    const url = 'http://localhost/aprendi/api/mensajesController.php';
    try {
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            curso_id: cursoSeleccionado,
            remitente_id: remitenteId,
            destinatario_id: destinatarioId,
            contenido: mensajeText
        })
    });
        if (!response.ok) {
            const errorText = await response.text();
            console.error("Error del servidor:", errorText);
            throw new Error('Error al enviar mensaje');
        }

        const jsonResponse = await response.json();
        console.log(jsonResponse);
        cargarMensajes(cursoSeleccionado, remitenteId);
        document.getElementById('mensaje').value = '';
    } catch (error) {
        console.error("Error al procesar el mensaje:", error);
    }
}
// Obtener el ID del instructor de un curso dado
async function obtenerInstructorCurso(cursoId) {
    try {
        const url = `http://localhost/aprendi/api/cursoController.php?id=${encodeURIComponent(cursoId)}`;
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error("Error al obtener detalles del curso");
        }

        const curso = await response.json();
        return curso.instructor_id;  // Asegúrate de que el objeto `curso` tiene `instructor_id`
    } catch (error) {
        console.error("Error al obtener el instructor del curso:", error);
        return null;
    }
}
// Cargar mensajes
async function cargarMensajes(cursoId, usuarioId) {
    try {
        // URL para recuperar todos los mensajes relacionados con el curso y usuario
        const url = `http://localhost/aprendi/api/mensajesController.php?id_curso=${cursoId}&id=${usuarioId}`;

        const response = await fetch(url);

        if (!response.ok) {
            throw new Error("Error al obtener los mensajes");
        }

        // Obtiene todos los mensajes en formato JSON
        let mensajes = await response.json();

        // Asegúrate de que la respuesta sea un array
        if (!Array.isArray(mensajes)) {
            console.warn("La respuesta no es un array. Ajustando...");
            mensajes = [mensajes];
        }

        // Limpiar el contenedor de mensajes
        const mensajesContainer = document.getElementById('mensajesContainer');
        mensajesContainer.innerHTML = '';

        // Mostrar todos los mensajes
        mensajes.forEach(mensaje => {
            const mensajeElement = document.createElement('div');

            // Determinar si el mensaje fue enviado o recibido por el usuario actual
            if (mensaje.remitente_id == usuarioId) {
                // Mensaje enviado por el usuario actual (lado derecho)
                mensajeElement.classList.add('mensaje', 'mensaje-usuario');
            } else if (mensaje.destinatario_id == usuarioId) {
                // Mensaje recibido por el usuario actual (lado izquierdo)
                mensajeElement.classList.add('mensaje', 'mensaje-instructor');
            }

            mensajeElement.innerHTML = `<p>${mensaje.contenido}</p><small>${mensaje.fecha}</small>`;
            mensajesContainer.appendChild(mensajeElement);
        });

    } catch (error) {
        console.error("Error al cargar los mensajes:", error);
    }
}
