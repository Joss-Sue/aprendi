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
        .then(data => {
            console.log("Datos recibidos del servidor:", data); // Verifica los datos aquí
            cursosContainer.innerHTML = ''; // Limpiar el contenedor UNA vez
    
            if (Array.isArray(data) && data.length > 0) {
                // Iterar solo una vez
                data.forEach(inscripcion => {
                    console.log("Curso ID:", inscripcion.curso_id);
                    console.log("Progreso desde API:", inscripcion.progreso_curso);
                    obtenerDetallesCurso(inscripcion.curso_id, inscripcion.estudiante_id);
                });
            } else {
                console.error("No hay cursos inscritos.");
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
    console.log(`Obteniendo detalles para cursoId: ${cursoId}, estudianteId: ${estudianteId}`);
    const urlCurso = `http://localhost/aprendi/api/cursoController.php?id=${encodeURIComponent(cursoId)}`;
    const urlProgreso = `http://localhost/aprendi/api/inscripcionesController.php?curso_id=${cursoId}&estudiante_id=${estudianteId}`;

    Promise.all([
        fetch(urlCurso).then(res => {
            console.log("Respuesta de cursoController:", res);
            return res.text(); // Cambiar a .text() para inspeccionar el contenido exacto
        }),
        fetch(urlProgreso).then(res => {
            console.log("Respuesta de progreso:", res);
            return res.text();
        })
    ])
    .then(([cursoResponse, progresoResponse]) => {
        console.log("Datos crudos de cursoController:", cursoResponse);
        console.log("Datos crudos de progresoController:", progresoResponse);
    
        // Intenta parsear JSON solo si es válido
        const curso = JSON.parse(cursoResponse);
        const progresoData = JSON.parse(progresoResponse);
    
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
        console.error('Error al procesar las solicitudes paralelas:', error);
    });
    
    
}
// Función para crear el elemento del curso con el progreso actualizado
function crearElementoCurso(curso, progreso) {
    console.log("Creando elemento para el curso:", curso, "Progreso:", progreso);
    const cursoDiv = document.createElement('div');
    cursoDiv.classList.add('curso-card');

    const titulo = curso.titulo || 'Sin título';
    const descripcion = curso.descripcion || 'Sin descripción';

    cursoDiv.innerHTML = `
    <div class="curso-card-content">
        <img src="${curso.imagen || '../img/default.png'}" alt="${titulo}" class="curso-img">
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
console.log("Elemento creado:", cursoDiv);
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
