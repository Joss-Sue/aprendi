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

async function cargarNiveles(cursoId) {
    const nivelesContainer = document.getElementById('nivelesContainer');
    nivelesContainer.innerHTML = '';

    try {
        // Obtener niveles desde la API
        const response = await fetch(`http://localhost/aprendi/api/nivelesController.php?curso_id=${cursoId}`);
        if (!response.ok) {
            throw new Error(`Error al obtener los niveles: ${response.status} - ${response.statusText}`);
        }
        const niveles = await response.json();

        // Obtener niveles completados por el estudiante
        const nivelesCompletados = await obtenerNivelesCompletados(cursoId, estudianteId);

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

            // Botón para completar nivel
            const completarBtn = document.createElement('button');
            completarBtn.textContent = 'Completar Nivel';
            completarBtn.classList.add('btn', 'btn-green');

            // Verificar si el nivel ya está completado
            if (nivelesCompletados.includes(nivel.nivel)) {
                completarBtn.disabled = true;
                completarBtn.textContent = 'Nivel Completado';
            } else {
                completarBtn.addEventListener('click', () => {
                    completarNivel(cursoId, nivel.nivel, completarBtn); // Pasa correctamente el nivel.nivel
                });
            }
            // Agregar evento al hacer clic en un nivel
            nivelItem.addEventListener('click', () => {
                cargarVideo(nivel); // Cargar el video y la descripción del nivel
            });
            nivelItem.appendChild(completarBtn);
            nivelesContainer.appendChild(nivelItem);
        });
    } catch (error) {
        console.error("Error al cargar niveles:", error);
        nivelesContainer.innerHTML = '<p>Error al cargar los niveles.</p>';
    }
}

function cargarVideo(nivel) {
    const videoContainer = document.getElementById('videoContainer'); 
    videoContainer.innerHTML = ''; 

    const blob = b64toBlob(nivel.url_video); // Convertir la base64 a Blob
    const blobUrl = URL.createObjectURL(blob); 

    const videoElement = document.createElement('video');
    videoElement.controls = true;
    videoElement.width = 760;
    videoElement.height = 415;
    videoElement.src = blobUrl;

    videoContainer.appendChild(videoElement);

    const nivelDescripcion = document.getElementById('nivelDescripcion');
    nivelDescripcion.textContent = nivel.descripcion || '[Descripción del nivel]';
}

function b64toBlob(base64) {
    // Extraer el encabezado (data:video/mp4;base64,)
    const parts = base64.split(';base64,');
    const contentType = parts[0].split(':')[1];
    const raw = atob(parts[1]);
    const rawLength = raw.length;
    const array = new Uint8Array(rawLength);

    for (let i = 0; i < rawLength; i++) {
        array[i] = raw.charCodeAt(i);
    }

    return new Blob([array], { type: contentType });
}


async function completarNivel(cursoId, nivelId, completarBtn) {
    if (!cursoId || !estudianteId) {
        alert("Error: No se pudo identificar el curso o el estudiante.");
        return;
    }
    console.log("Datos enviados al servidor:");
    console.log({
        curso_id: cursoId,
        nivel_id: nivelId,
        estudiante_id: estudianteId
    });
    try {
        const response = await fetch('http://localhost/aprendi/api/estudiantesNivelesController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                curso_id: cursoId,
                nivel_id: nivelId,
                estudiante_id: estudianteId
            })
        });
        console.log(await response.text())
        if (!response.ok) {
            throw new Error('Error al registrar nivel completado');
        }

        completarBtn.disabled = true;
        completarBtn.textContent = 'Nivel Completado';

        const nivelesCompletados = await obtenerNivelesCompletados(cursoId, estudianteId);
        if (nivelesCompletados.length === totalNiveles) {
            console.log("Curso completado. Actualizando estado...");
            await actualizarFechaTerminacion(cursoId, estudianteId);
        }
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
