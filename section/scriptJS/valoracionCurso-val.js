// Añadir funcionalidad de comentarios y valoraciones para usuarios que han terminado el curso

document.addEventListener('DOMContentLoaded', function () {
    // Verificar si el usuario puede comentar y valorar
    verificarPermisoComentario();
    cargarComentarios();
    // Verificar si el usuario ya ha comentado y valorado
    verificarComentarioExistente();
    cargarComentariosEliminados();
    // Manejar la publicación de comentarios y valoraciones
    document.getElementById('submitComment').addEventListener('click', function() {
        const valoracion = document.querySelector('input[name="star"]:checked')?.value;
        const comentario = document.getElementById('comment').value;
        if (valoracion) {
            publicarComentarioYValoracion(comentario, valoracion);
        } else {
            alert('Debe seleccionar una valoración.');
        }
    });
});

function verificarPermisoComentario() {
    const cursoId = obtenerCursoId(); // Funcionalidad para obtener el ID del curso actual
    fetch(`http://localhost/aprendi/api/inscripcionesController.php?curso_id=${cursoId}&estudiante_id=${estudianteId}`)
        .then(response => response.json())
        .then(data => {
            if (data && Number(data.progreso_curso) === 100) {
                // Si el curso está completado, permitir comentario y valoración
                document.getElementById('commentSection').style.display = 'block';
            } else {
                // Si no se ha completado el curso, ocultar la sección de comentarios
                document.getElementById('commentSection').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error al verificar si se puede comentar:', error);
            document.getElementById('commentSection').style.display = 'none';
        });
}

function verificarComentarioExistente() {
    const cursoId = obtenerCursoId();
    fetch(`http://localhost/aprendi/api/valoracionesController.php?id_curso=${cursoId}&id_estudiante=${estudianteId}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.id > 0) {
                document.getElementById('commentSection').style.display = 'none';
                //alert('Ya has valorado y comentado este curso.');
                if (!sessionStorage.getItem('recargado')) {
                    sessionStorage.setItem('recargado', 'true');
                    location.reload();
                }
            }
        })
        .catch(error => {
            console.error('Error al verificar si ya se ha comentado:', error);
        });
}

function publicarComentarioYValoracion(comentario, valoracion) {
    const cursoId = obtenerCursoId();

    if (!comentario || !valoracion) {
        mostrarMensajeError('error-comentario', 'Debe ingresar un comentario y una valoración.');
        return;
    }

    const data = {
        contenido: comentario,
        calificacion: valoracion,
        curso_id: cursoId,
        usuario_id: estudianteId
    };

    fetch('http://localhost/aprendi/api/valoracionesController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al publicar el comentario y la valoración');
        }
        return response.json();
    })
    .then(data => {
        //alert('Comentario y valoración publicados con éxito');
        // Actualizar la sección de comentarios
        document.getElementById('comment').value = '';
        document.getElementById('rating').checked = '';
        cargarComentarios();
    })
    .catch(error => {
        console.error('Error al publicar el comentario y la valoración:', error);
    });
}

function cargarComentarios() {
    const cursoId = obtenerCursoId();
    fetch(`http://localhost/aprendi/api/valoracionesController.php?id=${cursoId}`)
        .then(response => response.json())
        .then(data => {
            const comentariosContainer = document.getElementById('comentariosContainer');
            comentariosContainer.innerHTML = '';
            data.forEach(comentario => {
                // Hacer una petición para obtener la información del usuario
                fetch(`http://localhost/aprendi/api/usuariosController.php?id=${comentario.usuario_id}`)
                    .then(response => response.json())
                    .then(usuarioData => {
                        const comentarioElement = document.createElement('div');
                        comentarioElement.classList.add('comentario', 'card', 'mb-3', 'p-3', 'shadow-sm');
                        comentarioElement.innerHTML = `
                            <div class="d-flex align-items-center mb-2">
                                <img src="${usuarioData.foto || '../Imagenes/img-default.png'}" alt="Imagen del usuario" class="comentario-imagen rounded-circle me-3" style="width: 50px; height: 50px;">
                                <div>
                                    <p class="mb-0 fw-bold">${usuarioData.nombre}</p>
                                    <small class="text-muted">${new Date(comentario.fecha).toLocaleDateString()}</small>
                                </div>
                            </div>
                            <div class="comentario-body d-flex mt-2">
                                <div class="valoracion mb-0 fw-bold" style="margin-right: 0.2rem !important;margin-left: 0.7rem">${comentario.calificacion}</div>
                                <div class="valoracion mb-0 fw-bold" style="margin-right: 2rem !important; color: #ffc700;">★
                                </div>
                                <p class="mt-1 comentario-texto" style="max-height: 60px; overflow: hidden; text-align: left; text-overflow: ellipsis; white-space: nowrap;">${comentario.contenido}</p>
                                ${comentario.contenido.length > 100 ? '<button class="btn btn-link p-0 ver-mas" onclick="toggleVerMas(this)">Ver más</button>' : ''}
                            </div>
                            ${esAdmin() ? '<button class="btn btn-danger btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" onclick="prepararEliminarComentario(' + comentario.id + ')">Eliminar</button>' : ''}
                        `;
                        comentariosContainer.appendChild(comentarioElement);
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos del usuario:', error);
                    });
            });
        })
        .catch(error => {
            console.error('Error al cargar los comentarios:', error);
        });
}
function cargarComentariosEliminados() {
    const cursoId = obtenerCursoId();
    fetch(`http://localhost/aprendi/api/valoracionesController.php?ideli=${cursoId}`)
        .then(response => response.json())
        .then(data => {
            const comentariosContainer = document.getElementById('comentariosContainer');
            data.forEach(comentario => {
                // Hacer una petición para obtener la información del usuario
                fetch(`http://localhost/aprendi/api/usuariosController.php?id=${comentario.usuario_id}`)
                    .then(response => response.json())
                    .then(usuarioData => {
                        const comentarioElement = document.createElement('div');
                        comentarioElement.classList.add('comentario', 'card', 'mb-3', 'p-3', 'shadow-sm');
                        comentarioElement.innerHTML = `
                            <div class="d-flex align-items-center mb-2">
                                <img src="${usuarioData.foto || '../Imagenes/img-default.png'}" alt="Imagen del usuario" class="comentario-imagen rounded-circle me-3" style="width: 50px; height: 50px;">
                                <div>
                                    <p class="mb-0 fw-bold">${usuarioData.nombre}</p>
                                    <small class="text-muted">${new Date(comentario.fecha).toLocaleDateString()}</small>
                                </div>
                            </div>
                            <div class="comentario-body d-flex mt-2">
                                <p class="mt-1 comentario-texto" style="max-height: 60px; overflow: hidden; text-align: left; text-overflow: ellipsis; white-space: nowrap;">
                                    <i>Mensaje eliminado</i>
                                </p>
                            </div>
                        `;
                        comentariosContainer.appendChild(comentarioElement);
                    })
                    .catch(error => {
                        console.error('Error al obtener los datos del usuario:', error);
                    });
            });
        })
        .catch(error => {
            console.error('Error al cargar los comentarios eliminados:', error);
        });
}

function eliminarComentario(comentarioId) {
    //console.log('comnID', comentarioId);
    fetch(`http://localhost/aprendi/api/valoracionesController.php?id=${comentarioId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: comentarioId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al eliminar el comentario');
        }
        return response.json();
    })
    .then(data => {

        //alert('Comentario eliminado con éxito');
        location.reload();
        cargarComentariosEliminados();
    })
    .catch(error => {
        error.text().then(errMsg => {
            console.error('Error al eliminar el comentario:', errMsg);
        });
    });
}

function mostrarMensajeError(elementId, mensaje) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.innerText = mensaje;
        errorElement.style.color = 'red';
    }
}
// Funciones auxiliares para obtener el curso y el estudiante ID
function obtenerCursoId() {
    // Obtener el ID del curso desde la URL o desde un elemento en el DOM
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}
function esAdmin() {
    return usuarioRol == 'administrador';
}
function toggleVerMas(button) {
    const comentarioTexto = button.previousElementSibling;
    if (comentarioTexto.style.maxHeight === '60px') {
        comentarioTexto.style.maxHeight = 'none';
        comentarioTexto.style.whiteSpace = 'normal';
        button.textContent = 'Ver menos';
    } else {
        comentarioTexto.style.maxHeight = '60px';
        comentarioTexto.style.whiteSpace = 'nowrap';
        button.textContent = 'Ver más';
    }
}
function prepararEliminarComentario(comentarioId) {
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    confirmDeleteButton.setAttribute('onclick', `eliminarComentario(${comentarioId})`);
}
// Modal para confirmar eliminación de comentario
document.body.insertAdjacentHTML('beforeend', `
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar eliminación</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            ¿Está seguro de que desea eliminar este comentario?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="confirmDeleteButton">Eliminar</button>
          </div>
        </div>
      </div>
    </div>
    `);