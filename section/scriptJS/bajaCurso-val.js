document.addEventListener('DOMContentLoaded', function () {
    cargarCursosInstructor();
    cargarCategorias();
    
        const editarCursoModal = document.getElementById('editarCursoModal');
        editarCursoModal.addEventListener('hidden.bs.modal', function () {
            location.reload(); // Recarga la página
        });
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
                console.log(cursos.map(curso => ({ id: curso.id, imagen: curso.imagen }))); // Log de imágenes
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

function mostrarCursos(cursos) {
    const cursosContainer = document.querySelector('.row');
    cursosContainer.innerHTML = ''; // Limpiar contenido previo

    cursos.forEach(curso => {
        const cursoCard = document.createElement('div');
        cursoCard.classList.add('col-md-4');
        cursoId = curso.id;
        // Crear un Blob y una URL única para cada imagen
        let imageUrl = '';
        if (curso.imagen) {
            const blob = convertirBase64ABlob(curso.imagen);
            imageUrl = URL.createObjectURL(blob);
        } else {
            imageUrl = 'ruta_a_imagen_por_defecto'; // Por si no hay imagen
        }

        // Mostrar botones solo si el estado es "1"
        let botonesHTML = '';
        if (curso.estado == 1) {
            botonesHTML = `
                <button class="btn btn-green btn-sm" onclick="abrirModalEditar(${curso.id})">Editar</button>
                <button class="btn btn-danger btn-sm" onclick="darDeBajaCurso(${curso.id})">Dar de Baja</button>
            `;
        }

        cursoCard.innerHTML = `
            <div class="card">
                <img src="${imageUrl}" alt="${curso.titulo}" class="card-img-top" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">${curso.titulo}</h5>
                    <p class="card-text">${curso.descripcion}</p>
                    <p><strong>Categoría:</strong> ${curso.categoria_id}</p>
                    <p><strong>Costo:</strong> $${curso.costo}</p>
                    ${botonesHTML}
                </div>
            </div>
        `;

        // Revocar la URL cuando se elimine el elemento (opcional para evitar fugas de memoria)
        cursoCard.querySelector('img').onload = function () {
            URL.revokeObjectURL(imageUrl);
        };

        cursosContainer.appendChild(cursoCard);
    });
}




let cursoIdAEliminar = null;

function darDeBajaCurso(cursoId) {
    cursoIdAEliminar = cursoId; // Almacena el ID del curso que se va a dar de baja
    const confirmarModal = new bootstrap.Modal(document.getElementById('confirmarBajaModal'));
    confirmarModal.show();
}

// Manejar la confirmación de la baja
document.getElementById('btnConfirmarBaja').addEventListener('click', () => {
    if (cursoIdAEliminar) {
        const url = `http://localhost/aprendi/api/cursoController.php`;
        fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: cursoIdAEliminar }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('No se pudo dar de baja el curso.');
                }
                mostrarModalExito('Curso dado de baja exitosamente.');
                cargarCursosInstructor(); // Recargar la lista de cursos
            })
            .catch((error) => {
                console.error('Error al dar de baja el curso:', error);
            });
    }
});


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
        nivelDiv.setAttribute('data-nivel', nivel.nivel);
        nivelDiv.setAttribute('data-curso-id', nivel.curso_id); // Asignar curso_id

        // Crear un blob URL del video en base64
        const blob = convertirBase64ABlob(nivel.url_video);
        const videoURL = URL.createObjectURL(blob);

        nivelDiv.innerHTML = `
            <div class="mb-3">
                <h5>Nivel ${nivel.nivel}</h5>
                <label class="form-label">Descripción</label>
                <textarea id="nivelDescripcion${nivel.id}" class="form-control">${nivel.descripcion}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Video</label>
                <div class="d-flex align-items-center">
                    <input type="file" class="form-control me-2" id="nivelVideo${nivel.id}" accept="video/*" data-actual="${nivel.url_video}">
                    <button type="button" class="btn btn-info me-2" onclick="verVideo('${videoURL}')">Ver Video</button>
                    <button type="button" id="btnGuardarNivel${nivel.id}" class="btn btn-green" onclick="guardarEdicionNivel(${nivel.id})">Guardar</button>
                        <small id="errorNivelDescripcion${nivel.id}" class="text-danger"></small>
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
    if (!validarCurso()) {
        return; // No continuar si hay errores
    }
    const titulo = document.getElementById('cursoTitulo').value;
    const descripcion = document.getElementById('cursoDescripcion').value;
    const costo = document.getElementById('cursoCosto').value;
    const categoria = document.getElementById('cursoCategoria').value;

    const imagenInput = document.getElementById('cursoImagen');
    const nuevaImagen = imagenInput.files[0] || null;

    if (nuevaImagen) {
        const reader = new FileReader();
        reader.onload = function (event) {
            const base64Image = event.target.result; // Imagen convertida a base64
            enviarCurso(cursoId, titulo, descripcion, costo, categoria, base64Image);
        };
        reader.readAsDataURL(nuevaImagen);
    } else {
        // Si no hay una nueva imagen, usar la imagen actual como está
        const imagenActual = imagenInput.getAttribute('data-actual');
        enviarCurso(cursoId, titulo, descripcion, costo, categoria, imagenActual);
    }
}


function enviarCurso(id, titulo, descripcion, costo, categoria, imagen) {
    const data = {
        id,
        titulo,
        descripcion,
        costo,
        categoria: categoria,
        imagen,
    };

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
            mostrarModalExito('Curso actualizado correctamente.');
            document.getElementById('btnGuardarCurso').textContent = '✔';
            document.getElementById('btnGuardarCurso').disabled = true;
        })
        .catch((error) => {
            console.error('Error al guardar los cambios del curso:', error);
        });
}

function guardarEdicionNivel(nivelId) {
    if (!validarNivel(nivelId)) {
        return; // No continuar si hay errores
    }
    const nivelDiv = document.querySelector(`.nivel[data-id="${nivelId}"]`);
    const nivel = nivelDiv.getAttribute('data-nivel'); // Obtener el nivel desde el atributo
    const cursoId = nivelDiv.getAttribute('data-curso-id'); // Asegúrate de que `data-curso-id` esté definido

    const descripcion = document.querySelector(`#nivelDescripcion${nivelId}`).value;
    const videoInput = document.querySelector(`#nivelVideo${nivelId}`);
    const nuevoVideo = videoInput.files[0] || null;
    const videoActual = videoInput.getAttribute('data-actual');

    if (nuevoVideo) {
        const reader = new FileReader();
        reader.onload = function (event) {
            const base64Video = event.target.result; // Convertir video a base64
            enviarNivel(cursoId, nivel, descripcion, base64Video);
        };
        reader.readAsDataURL(nuevoVideo);
    } else {
        enviarNivel(cursoId, nivel, descripcion, videoActual);
    }
}
function enviarNivel(cursoId, nivel, descripcion, video) {
    const data = {
        curso_id: cursoId,
        nivel: nivel,
        descripcion: descripcion,
        url_video: video,
    };

    console.log('Datos enviados:', data); // Debug
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
            mostrarModalExito('Curso actualizado correctamente.');
            const btnGuardarNivel = document.querySelector(`#btnGuardarNivel${nivel}`);
            btnGuardarNivel.textContent = '✔';
            btnGuardarNivel.disabled = true;
        })
        .catch((error) => {
            console.error('Error al guardar los cambios del nivel:', error);
        });
}
function mostrarModalExito(mensaje) {
    const modalMessage = document.getElementById('successModalMessage');
    modalMessage.textContent = mensaje; 
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show(); 
}
function validarCurso() {
    let esValido = true;

    // Validar título
    const titulo = document.getElementById('cursoTitulo').value.trim();
    const errorTitulo = document.getElementById('errorCursoTitulo');
    if (titulo === '' || titulo.length < 3 || /^\d+$/.test(titulo)) {
        errorTitulo.textContent = 'El título debe tener al menos 3 caracteres y no puede ser solo números.';
        esValido = false;
    } else {
        errorTitulo.textContent = '';
    }

    // Validar descripción
    const descripcion = document.getElementById('cursoDescripcion').value.trim();
    const errorDescripcion = document.getElementById('errorCursoDescripcion');
    if (descripcion === '' || descripcion.length < 10) {
        errorDescripcion.textContent = 'La descripción debe tener al menos 10 caracteres.';
        esValido = false;
    } else {
        errorDescripcion.textContent = '';
    }

    // Validar costo
    const costo = document.getElementById('cursoCosto').value.trim();
    const errorCosto = document.getElementById('errorCursoCosto');
    if (isNaN(costo) || costo <= 0) {
        errorCosto.textContent = 'El costo debe ser un número mayor a 0.';
        esValido = false;
    } else {
        errorCosto.textContent = '';
    }

    // Validar categoría
    const categoria = document.getElementById('cursoCategoria').value.trim();
    const errorCategoria = document.getElementById('errorCursoCategoria');
    if (categoria === '') {
        errorCategoria.textContent = 'Debes seleccionar una categoría.';
        esValido = false;
    } else {
        errorCategoria.textContent = '';
    }

    return esValido;
}

function validarNivel(nivelId) {
    let esValido = true;

    // Validar descripción
    const descripcion = document.querySelector(`#nivelDescripcion${nivelId}`).value.trim();
    const errorDescripcion = document.querySelector(`#errorNivelDescripcion${nivelId}`);
    if (descripcion === '' || descripcion.length < 5) {
        errorDescripcion.textContent = 'La descripción debe tener al menos 5 caracteres.';
        esValido = false;
    } else {
        errorDescripcion.textContent = '';
    }

    return esValido;
}
