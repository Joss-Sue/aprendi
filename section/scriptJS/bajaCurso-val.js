document.addEventListener('DOMContentLoaded', function () {
    cargarCursosInstructor();
    cargarCategorias();
});
function cargarCategorias() {
    const url = 'http://localhost/aprendi/api/categoriaController.php';

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la API de categorías: ${response.status}`);
            }
            return response.json();
        })
        .then(categorias => {
            const categoriaSelect = document.getElementById('cursoCategoria');
            categoriaSelect.innerHTML = '<option value="">Selecciona una categoría</option>';

            categorias.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.id; // Asegúrate de que `id` sea el nombre del campo en la respuesta
                option.textContent = categoria.nombre; // Cambia `nombre` si el campo tiene otro nombre en tu API
                categoriaSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar las categorías:', error);
        });
}

// Cargar los cursos registrados por el instructor
function cargarCursosInstructor() {
    const url = `http://localhost/aprendi/api/cursoController.php?pagina=1&id=${usuarioId}`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la API: ${response.status}`);
            }
            return response.json();
        })
        .then(cursos => {
            if (Array.isArray(cursos) && cursos.length > 0) {
                mostrarCursos(cursos);
            } else {
                document.querySelector('.row').innerHTML = '<p>No tienes cursos registrados.</p>';
            }
        })
        .catch(error => {
            console.error("Error al cargar los cursos:", error);
            document.querySelector('.row').innerHTML = '<p>Error al cargar los cursos.</p>';
        });
}
function obtenerNiveles(cursoId) {
    const url = `http://localhost/aprendi/api/nivelesController.php?curso_id=${cursoId}`;

    return fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error al obtener los niveles: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error("Error al obtener los niveles:", error);
            return []; // Devuelve un array vacío si hay un error
        });
}

// Mostrar los cursos en la página
function mostrarCursos(cursos) {
    const cursosContainer = document.querySelector('.row');
    cursosContainer.innerHTML = ''; // Limpiar contenido previo

    cursos.forEach(curso => {
        const cursoCard = document.createElement('div');
        cursoCard.classList.add('col-md-4');
        console.log(curso.id);
        cursoId = curso.id;
        cursoCard.innerHTML = `
            <div class="card">
                <img src="${curso.imagen}" alt="${curso.titulo}" class="card-img-top" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">${curso.titulo}</h5>
                    <p class="card-text">${curso.descripcion}</p>
                    <p><strong>Categoría:</strong> ${curso.categoria}</p>
                    <p><strong>Costo:</strong> $${curso.costo}</p>
                    <button class="btn btn-primary btn-sm" onclick="abrirModalEditar(${curso.id})">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="darDeBajaCurso(${curso.id})">Dar de Baja</button>
                </div>
            </div>
        `;

        cursosContainer.appendChild(cursoCard);
    });
}
function darDeBajaCurso(cursoId) {
    if (!confirm("¿Estás seguro de que deseas dar de baja este curso?")) return;

    const url = `http://localhost/aprendi/api/cursoController.php`;
    fetch(url, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: cursoId })
    })
        .then(response => {
            if (!response.ok) {
                throw new Error("No se pudo dar de baja el curso.");
            }
            alert("Curso dado de baja exitosamente.");
            cargarCursosInstructor(); // Recargar lista de cursos
        })
        .catch(error => {
            console.error("Error al dar de baja el curso:", error);
        });
}
function abrirModalEditar(cursoId) {
    const urlCurso = `http://localhost/aprendi/api/cursoController.php/?id=${cursoId}`;
    const urlNiveles = `http://localhost/aprendi/api/nivelesController.php?curso_id=${cursoId}`;

    Promise.all([
        fetch(urlCurso).then(res => res.json()),
        fetch(urlNiveles).then(res => res.json())
    ])
        .then(([curso, niveles]) => {
            // Llenar datos del curso en el formulario
            document.getElementById('cursoTitulo').value = curso.titulo;
            document.getElementById('cursoDescripcion').value = curso.descripcion;
            document.getElementById('cursoCosto').value = curso.costo;
            document.getElementById('cursoCategoria').value = curso.categoria_id;

            const imagenInput = document.getElementById('cursoImagen');
            imagenInput.setAttribute('data-actual', curso.imagen);

            // Mostrar niveles en el contenedor
            mostrarNiveles(niveles);

            // Mostrar el modal
            const editarCursoModal = new bootstrap.Modal(document.getElementById('editarCursoModal'));
            editarCursoModal.show();
        })
        .catch(error => {
            console.error("Error al obtener los detalles del curso y niveles:", error);
        });
}
function mostrarNiveles(niveles) {
    const nivelesContainer = document.getElementById('nivelesContainer');
    nivelesContainer.innerHTML = ''; // Limpiar contenido previo

    niveles.forEach(nivel => {
        const nivelDiv = document.createElement('div');
        nivelDiv.classList.add('nivel');
        nivelDiv.setAttribute('data-id', nivel.id);

        // Crear un blob URL del video en base64
        const blob = convertirBase64ABlob(nivel.url_video);
        const videoURL = URL.createObjectURL(blob);

        nivelDiv.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Nivel ${nivel.nivel}</label>
                <textarea class="form-control nivel-descripcion">${nivel.descripcion}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Video</label>
                <div class="d-flex align-items-center">
                    <input type="file" class="form-control me-2" id="videoInputNivel${nivel.id}" accept="video/*">
                    <button type="button" class="btn btn-info" onclick="verVideo('${videoURL}')">Ver Video</button>
                </div>
            </div>
        `;

        nivelesContainer.appendChild(nivelDiv);
    });
}

function procesarVideoNivel(nivel) {
    const videoBlob = nivel.url_video; // La URL base64 ya se obtiene procesada desde el backend.
    const blob = convertirBase64ABlob(videoBlob);
    const videoURL = URL.createObjectURL(blob); // Crear URL temporal del video

    // Rellenar el input del archivo con la URL temporal
    const videoInput = document.querySelector(`#videoInputNivel${nivel.id}`);
    videoInput.setAttribute('data-current-url', videoURL); // Guardar URL para "Ver Video"
}

function convertirBase64ABlob(base64) {
    const base64Parts = base64.split(';base64,');
    const contentType = base64Parts[0].split(':')[1];
    const rawData = atob(base64Parts[1]);
    const array = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; i++) {
        array[i] = rawData.charCodeAt(i);
    }

    return new Blob([array], { type: contentType });
}

function verVideo(videoUrl) {
    const modal = document.getElementById('verVideoModal');
    const videoElement = modal.querySelector('video');

    videoElement.src = videoUrl; // Asignar la URL del video
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

function guardarEdicionCurso() {
    const titulo = document.getElementById('cursoTitulo').value;
    const descripcion = document.getElementById('cursoDescripcion').value;
    const costo = document.getElementById('cursoCosto').value;
    const categoria = document.getElementById('cursoCategoria').value;

    const imagenInput = document.getElementById('cursoImagen');
    const nuevaImagen = imagenInput.files[0] || null;
    const imagenActual = imagenInput.getAttribute('data-actual');

    const data = {
        id: cursoId,
        titulo,
        descripcion,
        costo,
        categoria_id: categoria,
        imagen: nuevaImagen ? nuevaImagen : imagenActual,
    };
    console.log("Datos enviados:", data);

    fetch(`http://localhost/aprendi/api/cursoController.php`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Error al actualizar el curso.');
            }
            alert('Curso actualizado correctamente.');
            document.getElementById('btnGuardarCurso').textContent = '✔';
            document.getElementById('btnGuardarCurso').disabled = true;
        })
        .catch((error) => {
            console.error('Error al guardar los cambios del curso:', error);
        });
}
function guardarEdicionNivel(nivelId) {
    const descripcion = document.querySelector(`#nivelDescripcion${nivelId}`).value;
    const videoInput = document.querySelector(`#nivelVideo${nivelId}`);
    const nuevoVideo = videoInput.files[0] || null;
    const videoActual = videoInput.getAttribute('data-actual');

    const data = {
        id: nivelId,
        descripcion,
        video: nuevoVideo ? nuevoVideo : videoActual,
    };

    fetch(`http://localhost/aprendi/api/nivelesController.php`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Error al actualizar el nivel.');
            }
            alert('Nivel actualizado correctamente.');
            const btnGuardarNivel = document.querySelector(`#btnGuardarNivel${nivelId}`);
            btnGuardarNivel.textContent = '✔';
            btnGuardarNivel.disabled = true;
        })
        .catch((error) => {
            console.error('Error al guardar los cambios del nivel:', error);
        });
}

