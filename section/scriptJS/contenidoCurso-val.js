let progresoActual = 0;
let totalNiveles = 0;
document.addEventListener('DOMContentLoaded', function () {
    fetch('../partials/menu.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('menu-container').innerHTML = data;
        });

    fetch('../partials/footer.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer-container').innerHTML = data;
        });

    const urlParams = new URLSearchParams(window.location.search);
    const cursoId = urlParams.get('id');
    if (!cursoId|| !estudianteId) {
        alert("No se pudo obtener el ID del curso.");
        return;
    }
    console.log("Estudiante ID:", estudianteId);
    
    // Cargar datos
    obtenerProgreso(cursoId);
    obtenerTotalNiveles(cursoId);
    obtenerNivelesCompletados(cursoId, estudianteId);
    cargarCurso(cursoId);
    cargarNiveles(cursoId);

    // Agregar evento al botón para completar nivel
    const completarBtn = document.getElementById('completarNivelBtn');
    if (completarBtn) {
        completarBtn.addEventListener('click', function () {
            completarNivel(cursoId);
        });
    }
});

// Función para obtener el progreso actual del curso
function obtenerProgreso(cursoId) {
    return fetch(`http://localhost/aprendi/api/inscripcionesController.php?curso_id=${cursoId}&estudiante_id=${estudianteId}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.progreso_curso) {
                progresoActual = parseFloat(data.progreso_curso);
                console.log("Progreso obtenido:", progresoActual);
            } else {
                progresoActual = 0;
            }
        })
        .catch(error => {
            console.error("Error al obtener el progreso:", error);
        });
}

function obtenerTotalNiveles(cursoId) {
    fetch(`http://localhost/aprendi/api/nivelesController.php?curso_id=${cursoId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la API: ${response.status} - ${response.statusText}`);
            }
            return response.json();
        })
        .then(niveles => {
            if (niveles && Array.isArray(niveles)) {
                totalNiveles = niveles.length;
                console.log("Total de niveles:", totalNiveles);
            } else {
                console.error("Error: Respuesta inválida para niveles:", niveles);
            }
        })
        .catch(error => {
            console.error("Error al obtener los niveles:", error);
        });
}

// Función para cargar el título del curso
function cargarCurso(cursoId) {
    fetch(`http://localhost/aprendi/api/cursoController.php?id=${cursoId}`)
        .then(response => response.json())
        .then(curso => {
            document.getElementById('cursoTitulo').textContent = curso.titulo || 'Sin título';
        })
        .catch(error => {
            console.error("Error al obtener el curso:", error);
        });
}
async function obtenerNivelesCompletados(cursoId, estudianteId) {
    try {
        const response = await fetch(`http://localhost/aprendi/api/estudiantesNivelesController.php?curso_id=${cursoId}&estudiante_id=${estudianteId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (Array.isArray(data)) {
            console.log("Niveles completados obtenidos:", data);
            return data.map(nivel => nivel.nivel_id);
        } else {
            //console.error("La respuesta no es un array:", data);
            return [];
        }
    } catch (error) {
        //console.error("Error al obtener niveles completados:", error);
        return [];
    }
}


async function cargarNiveles(cursoId) {
    const nivelesContainer = document.getElementById('nivelesContainer');
    nivelesContainer.innerHTML = '';

    try {
        const response = await fetch(`http://localhost/aprendi/api/nivelesController.php?curso_id=${cursoId}`);
        if (!response.ok) {
            throw new Error(`Error al obtener los niveles: ${response.status} - ${response.statusText}`);
        }
        const niveles = await response.json();

        if (!niveles || niveles.length === 0) {
            nivelesContainer.innerHTML = '<p>No hay niveles disponibles.</p>';
            return;
        }

        // Mostrar niveles
        niveles.forEach(nivel => {
            const nivelItem = document.createElement('div');
            nivelItem.classList.add('nivel-card');

            const nivelTitulo = document.createElement('h5');
            nivelTitulo.textContent = `Nivel ${nivel.nivel}: ${nivel.descripcion}`;
            nivelItem.appendChild(nivelTitulo);

            nivelesContainer.appendChild(nivelItem);
        });
    } catch (error) {
        console.error("Error al cargar niveles:", error);
        nivelesContainer.innerHTML = '<p>Error al cargar los niveles.</p>';
    }
}
function transformarUrlYoutube(url) {
    if (url.includes("watch?v=")) {
        return url.replace("watch?v=", "embed/");
    } else if (url.includes("youtu.be/")) {
        return url.replace("youtu.be/", "youtube.com/embed/");
    }
    return url; // Devuelve la misma si ya está en formato embed o no necesita cambios
}

function cargarVideo(nivel) {
    const videoContainer = document.getElementById('videoContainer'); // Un contenedor para el video iframe
    videoContainer.innerHTML = ''; // Limpiar el contenedor

    const urlTransformada = transformarUrlYoutube(nivel.url_video);
    console.log("URL transformada para el iframe:", urlTransformada); // Agrega este log para verificar la URL

    const videoFrame = document.createElement('iframe');
    videoFrame.width = '760';
    videoFrame.height = '415';
    videoFrame.src = urlTransformada;  // Usa la URL transformada aquí
    videoFrame.title = 'YouTube video player';
    videoFrame.frameBorder = '0';
    videoFrame.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
    videoFrame.allowFullscreen = true;

    videoContainer.appendChild(videoFrame);

    // Mostrar la descripción debajo del video
    const nivelDescripcion = document.getElementById('nivelDescripcion');
    nivelDescripcion.textContent = nivel.descripcion || '[Descripción del nivel]';
}

async function completarNivel(cursoId, nivelId, completarBtn) {
    if (!cursoId || !estudianteId) {
        alert("Error: No se pudo identificar el curso o el estudiante.");
        return;
    }

    try {
        await registrarNivelCompletado(cursoId, nivelId, estudianteId);

        // Deshabilitar el botón después de completar el nivel
        completarBtn.disabled = true;
        completarBtn.textContent = 'Nivel Completado';

        // Verificar si todos los niveles están completados
        const nivelesCompletados = await obtenerNivelesCompletados(cursoId, estudianteId);
        const totalNiveles = await obtenerTotalNivelesCount(cursoId);

        if (nivelesCompletados.length === totalNiveles) {
            console.log("Todos los niveles han sido completados. Actualizando la fecha de terminación...");
            await actualizarFechaTerminacion(cursoId, estudianteId);
        }

        // Volver a cargar los niveles para actualizar los botones
        await cargarNiveles(cursoId);
    } catch (error) {
        console.error("Error al completar nivel:", error);
    }
}

// Función para obtener el número total de niveles del curso
async function obtenerTotalNivelesCount(cursoId) {
    try {
        const response = await fetch(`http://localhost/aprendi/api/nivelesController.php?curso_id=${cursoId}`);
        const niveles = await response.json();
        return niveles.length;
    } catch (error) {
        console.error("Error al obtener el total de niveles:", error);
        return 0;
    }
}

// Función para actualizar la fecha de terminación del curso
async function actualizarFechaTerminacion(cursoId, estudianteId) {
    try {
        const response = await fetch(`http://localhost/aprendi/api/inscripcionesController.php`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                curso_id: cursoId,
                estudiante_id: estudianteId,
                tipo: "terminacion",
                progreso: 0
            })
        });

        if (response.ok) {
            console.log("Fecha de terminación actualizada correctamente.");
        } else {
            console.error("Error al actualizar la fecha de terminación.");
        }
    } catch (error) {
        console.error("Error al actualizar la fecha de terminación:", error);
    }
}

async function registrarNivelCompletado(cursoId, nivelId, usuarioId) {
    try {
        fetch('http://localhost/aprendi/api/estudiantesNivelesController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                curso_id: cursoId,
                nivel_id: nivelId,
                estudiante_id: usuarioId
            })
        });
        const data = JSON.parse(dataText);

        if (data.status === "success") {
            console.log(`Nivel ${nivelId} registrado para el curso ${cursoId}`);
        } else {
            console.error("Error al registrar nivel:", data.message);
        }
    } catch (error) {
        console.error("Error al registrar nivel:", error);
    }
}


