let progresoActual = 0;
document.addEventListener('DOMContentLoaded', function () {
    const cursosContainer = document.getElementById('cursosContainer');

    // Verificar que el usuarioId se está pasando correctamente desde PHP
    if (typeof usuarioId === 'undefined' || !usuarioId) {
        alert("No se pudo obtener el ID del usuario.");
        return;
    }

    // Llamar a la función para cargar los cursos inscritos
    cargarCursosInscritos(usuarioId);

     // Función para cargar los cursos inscritos usando fetch
    function cargarCursosInscritos(usuarioId) {
        const url = `http://localhost/aprendi/api/inscripcionesController.php?id=${encodeURIComponent(usuarioId)}`;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la respuesta del servidor");
            }
            return response.json();
        })
        .then(inscripciones => {
            if (Array.isArray(inscripciones) && inscripciones.length > 0) {
                cursosContainer.innerHTML = ''; // Limpiar el contenedor antes de agregar los cursos

                // Hacer una solicitud para cada curso inscrito y obtener su progreso
                inscripciones.forEach(inscripcion => {
                    console.log("Curso ID:", inscripcion.curso_id);
                    console.log("Progreso desde API:", inscripcion.progreso_curso);
                    obtenerDetallesCurso(inscripcion.curso_id, inscripcion.estudiante_id);
                });
            } else {
                cursosContainer.innerHTML = '<p>No tienes cursos inscritos.</p>';
            }
        })
        .catch(error => {
            console.error('Error al obtener los cursos inscritos:', error);
            cursosContainer.innerHTML = '<p>Error al cargar los cursos.</p>';
        });
    }

    // Función para obtener los detalles del curso usando el cursoController.php
function obtenerDetallesCurso(cursoId, estudianteId) {
    const urlCurso = `http://localhost/aprendi/api/cursoController.php?id=${encodeURIComponent(cursoId)}`;
    const urlProgreso = `http://localhost/aprendi/api/inscripcionesController.php?curso_id=${cursoId}&estudiante_id=${estudianteId}`;

    // Obtener detalles del curso y el progreso en paralelo
    Promise.all([
        fetch(urlCurso).then(res => res.json()),
        fetch(urlProgreso).then(res => res.json())
    ])
    .then(([curso, progresoData]) => {
        if (curso && curso.id) {
            const progreso = progresoData && progresoData.progreso_curso ? parseFloat(progresoData.progreso_curso) : 0;
            console.log(`Progreso calculado para el curso ${curso.id}: ${progreso}%`);
            const cursoElement = crearElementoCurso(curso, progreso);
            cursosContainer.appendChild(cursoElement);
        } else {
            console.error("No se encontraron detalles para el curso con ID:", cursoId);
        }
    })
    .catch(error => {
        console.error('Error al obtener los detalles del curso o el progreso:', error);
    });
}
// Función para crear el elemento del curso con el progreso actualizado
function crearElementoCurso(curso, progreso) {
    const cursoDiv = document.createElement('div');
    cursoDiv.classList.add('curso-card');

    const titulo = curso.titulo || 'Sin título';
    const descripcion = curso.descripcion || 'Sin descripción';

    cursoDiv.innerHTML = `
        <div class="curso-card-content">
            <img src="../img/default.png" alt="${titulo}" class="curso-img">
            <div class="curso-info">
                <h5>${titulo}</h5>
                <p>${descripcion}</p>
                <div class="progreso">
                    <div class="progreso-bar" style="width: ${progreso}%"></div>
                </div>
                <p>Progreso: ${progreso.toFixed(2)}%</p>
                <a href="../cursos/contenido-curso.php?id=${curso.id}" class="btn btn-green comenzar-curso-btn" data-curso-id="${curso.id}" data-estudiante-id="${usuarioId}">Comenzar</a>
            </div>
        </div>
    `;

        // Añadir evento para actualizar el último acceso
        const comenzarBtn = cursoDiv.querySelector('.comenzar-curso-btn');
        comenzarBtn.addEventListener('click', async (event) => {
            event.preventDefault(); // Evitar redireccionamiento inmediato
    
            const cursoId = event.target.dataset.cursoId;
            const estudianteId = event.target.dataset.estudianteId;
    
            await actualizarUltimoAcceso(cursoId, estudianteId);
            
            // Redirigir al contenido del curso después de actualizar el último acceso
            window.location.href = event.target.href;
        });
    return cursoDiv;
}
});

// Función para actualizar la fecha de último acceso del curso para el estudiante
async function actualizarUltimoAcceso(cursoId, estudianteId) {
    const url = `http://localhost/aprendi/api/inscripcionesController.php`;
    try {
        const response = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                curso_id: cursoId,
                estudiante_id: estudianteId,
                tipo: 'ultima',  // Usar "ultima" para indicar que queremos actualizar la fecha de último acceso
                progreso: 0 // No estamos cambiando el progreso, pero el API requiere este campo, así que le pasamos 0
            })
        });

        if (!response.ok) {
            throw new Error("Error al actualizar la fecha de último acceso");
        }

        console.log("Fecha de último acceso actualizada correctamente");
    } catch (error) {
        console.error("Error al actualizar el último acceso:", error);
    }
}
