document.addEventListener('DOMContentLoaded', function () {
    const formRegistroCurso = document.getElementById('registroCursoForm');
    const comboBox = document.getElementById('categoria');
    const cantidadNivelesInput = document.getElementById('cantidad_niveles');
    const costoTotalInput = document.getElementById('costo_total');
    const costoPorNivelInput = document.getElementById('costo_por_nivel');
    const tituloInput = document.getElementById('titulo');
    const descripcionInput = document.getElementById('descripcion');
    const nivelesContainer = document.getElementById('nivelesContainer');
    const imagenInput = document.getElementById('imagenCurso');
    const cantidadNivelesInputDos = document.getElementById('cantidad_niveles_nuevo');
    
    const usuarioId = document.getElementById('usuarioId') ? document.getElementById('usuarioId').value : null;
    if (!usuarioId) {
        console.error("El ID del usuario no está definido.");
        return; // Si no hay ID de usuario, se detiene la ejecución
    }

    // Cargar categorías
    if (comboBox) {
        cargarCategorias();
    } else {
        console.error("No se encontró el elemento con id 'categoria'");
    }

    // Calcular costo por nivel automáticamente
function calcularCostoPorNivel() {
        const cantidadNiveles = parseInt(cantidadNivelesInput.value, 10);
        const costoTotal = parseFloat(costoTotalInput.value);

        if (cantidadNiveles && costoTotal) {
            const costoPorNivel = (costoTotal / cantidadNiveles).toFixed(2);
            costoPorNivelInput.value = costoPorNivel;
        } else {
            costoPorNivelInput.value = '';
        }
}

// Verifica que los elementos existan
if (!cantidadNivelesInput || !nivelesContainer) {
    console.error("No se encontraron los elementos necesarios en el DOM");
    return;
}

// Validar y enviar el formulario
if (formRegistroCurso) {
        formRegistroCurso.addEventListener('submit', function (event) {
            event.preventDefault();

            limpiarMensajesError();
            let valid = true;

            // Validaciones
            if (!comboBox.value) {
                mostrarMensajeError('error-categoria', 'Debe seleccionar una categoría.');
                valid = false;
            }

            if (!tituloInput.value || /^[^a-zA-Z0-9]*$/.test(tituloInput.value)) {
                mostrarMensajeError('error-titulo', 'El título no puede estar vacío o ser solo símbolos.');
                valid = false;
            }

            if (!cantidadNivelesInput.value || isNaN(cantidadNivelesInput.value) || cantidadNivelesInput.value <= 0) {
                mostrarMensajeError('error-cantidad_niveles', 'Debe ingresar una cantidad de niveles válida.');
                valid = false;
            }

            if (!costoTotalInput.value || isNaN(costoTotalInput.value) || costoTotalInput.value <= 0) {
                mostrarMensajeError('error-costo_total', 'Debe ingresar un costo total válido.');
                valid = false;
            }

            if (!descripcionInput.value) {
                mostrarMensajeError('error-descripcion', 'Debe ingresar una descripción.');
                valid = false;
            }
            if (imagenInput.files.length === 0) {
                mostrarMensajeError('error-imagenCurso', 'Debe seleccionar una imagen.');
                valid = false;
            }

            if (valid) {
                convertirImagenABase64(imagenInput.files[0], function (imagenBase64) {
                    enviarFormularioCurso(usuarioId, imagenBase64);
                });
            }
        });
    } else {
        console.error("No se encontró el formulario de registro de curso.");
    }

    // Calcular costo por nivel cuando cambian cantidad de niveles o costo total
    cantidadNivelesInput.addEventListener('input', calcularCostoPorNivel);
    costoTotalInput.addEventListener('input', calcularCostoPorNivel);

    // Deshabilitar el input de costo por nivel
    costoPorNivelInput.setAttribute('readonly', true);
});

// Función para convertir la imagen a Base64
function convertirImagenABase64(file, callback) {
    const reader = new FileReader();
    reader.onloadend = function () {
        callback(reader.result);
    };
    reader.readAsDataURL(file);
}

// Cargar categorías desde la API
function cargarCategorias() {
    fetch('http://localhost/aprendi/api/categoriaController.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(categorias => {
        const comboBox = document.getElementById('categoria');
        comboBox.innerHTML = '<option value="">Seleccione una categoría</option>';

        categorias.forEach(categoria => {
            const option = document.createElement('option');
            option.value = categoria.id;
            option.textContent = categoria.nombre;
            comboBox.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error al cargar las categorías:', error.message);
    });
}
// Mostrar mensajes de error en rojo
function mostrarMensajeError(elementId, mensaje) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.innerText = mensaje;
        errorElement.style.color = 'red';
    }
}
// Limpiar mensajes de error
function limpiarMensajesError() {
    document.querySelectorAll('.error-message').forEach(error => error.innerText = '');
}
// Modal de éxito
function mostrarModalExito() {
    const modal = new bootstrap.Modal(document.getElementById('modalExito'));
    modal.show();

    document.getElementById('cerrarModal').addEventListener('click', function () {
        document.getElementById('registroCursoForm').reset();
        document.getElementById('costo_por_nivel').value = '';
    });
}

// Enviar formulario de curso a la API
function enviarFormularioCurso(usuarioId, imagenBase64) {
    const formData = new FormData(document.getElementById('registroCursoForm'));
    const data = {
        titulo: formData.get('titulo'),
        descripcion: formData.get('descripcion'),
        costo: formData.get('costo_total'),
        instructor: usuarioId,
        categoria: formData.get('categoria'),
        imagen: imagenBase64
    };

    console.log('Datos a enviar:', JSON.stringify(data));

    fetch('http://localhost/aprendi/api/cursoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(`Error en la solicitud: ${errorData.message || 'Sin mensaje de error'}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log("Respuesta de la API: ", data); // Log para diagnosticar la respuesta de la API
        if (data.success) {
            // Mostrar el formulario de niveles y ocultar el de curso
            document.getElementById('registroCursoForm').style.display = 'none';
            document.getElementById('nivelesForm').style.display = 'block';

            // Actualizar el combo box con el último curso registrado
            mostrarComboBoxCursos(usuarioId);
            const cantidadNiveles = document.getElementById('cantidad_niveles').value;
            generarInputsNiveles(cantidadNiveles);
            
        } else {
            throw new Error(`Error del servidor: ${data.message || 'Sin mensaje de error'}`);
        }
    })
    .catch(error => {
        console.error('Error al registrar el curso:', error);
        alert(`Error al registrar el curso: ${error.message}`);
    });
}

function mostrarComboBoxCursos(usuarioId) {
    // Hacer una solicitud POST al nuevo endpoint para obtener el último curso
    fetch('http://localhost/aprendi/api/ultimoIDCurso.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: usuarioId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al obtener el último curso del instructor');
        }
        return response.json();
    })
    .then(data => {
        if (Array.isArray(data) && data.length > 0) {
            const curso = data[0]; // Obtenemos el curso
            console.log('Último curso obtenido:', curso);
        
            const cursosSelect = document.getElementById('cursosSelect');
            cursosSelect.innerHTML = ''; // Limpiamos el combobox
        
            const option = document.createElement('option');
            option.value = curso.id;
            option.textContent = curso.titulo; // Mostramos el título
            cursosSelect.appendChild(option);
        
            cursosSelect.disabled = true;
        
            // Mostrar el formulario de niveles
            document.getElementById('nivelesForm').style.display = 'block';
        }
        
    })
    .catch(error => {
        console.error('Error al obtener el último curso:', error);
    });
}



function obtenerCursoMasReciente(cursos) {
    let ultimoCurso = null;
    let ultimaFecha = new Date(0); // Fecha mínima para comparación

    cursos.forEach(curso => {
        const fechaCreacion = new Date(curso.fecha_creacion);
        if (fechaCreacion > ultimaFecha) {
            ultimaFecha = fechaCreacion;
            ultimoCurso = curso;
        }
    });

    return ultimoCurso;
}

function generarInputsNiveles(cantidadNiveles) {
    const nivelesContainer = document.getElementById('nivelesContainer');
    nivelesContainer.innerHTML = ''; // Limpiar niveles previos

    if (isNaN(cantidadNiveles) || cantidadNiveles <= 0) return;

    for (let i = 1; i <= cantidadNiveles; i++) {
        const nivelDiv = document.createElement('div');
        nivelDiv.classList.add('nivel-input', 'mb-3');
        nivelDiv.innerHTML = `
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading${i}">
                    <button class="accordion-button collapsed nivel-boton" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${i}" aria-expanded="false" aria-controls="collapse${i}">
                        Nivel ${i}
                    </button>
                </h2>
                <div id="collapse${i}" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <label for="nivel_${i}_video">Subir video para Nivel ${i}:</label>
                        <input type="file" id="nivel_${i}_video" class="form-control" accept="video/*">
                        <small class="error-message" id="error-url-${i}" style="color:red; margin-bottom:15px;"></small>
                        <br>
                        <br>
                        <label for="nivel_${i}_descripcion" class="form-label">Descripción:</label>
                        <textarea id="nivel_${i}_descripcion" class="form-control nivel-descripcion" placeholder="Descripción del nivel ${i}"></textarea>
                        <small style="margin-bottom: 15px" class="error-message" id="error-descripcion-${i}" style="color:red; margin-bottom:15px;"></small>
                    </div>
                </div>
            </div>`;
        nivelesContainer.appendChild(nivelDiv);
    }
}


async function registrarNiveles() {
    const nivelesVideos = document.querySelectorAll('.nivel-input input[type="file"]');
    const nivelesDescripciones = document.querySelectorAll('.nivel-descripcion');
    const cursoId = document.getElementById('cursosSelect').value;

    let valid = true;
    const nivelesData = [];

    // Validar todos los niveles
    for (let index = 0; index < nivelesVideos.length; index++) {
        const videoInput = nivelesVideos[index];
        const descripcionInput = nivelesDescripciones[index];
        const videoError = document.getElementById(`error-url-${index + 1}`);
        const descripcionError = document.getElementById(`error-descripcion-${index + 1}`);

        if (!videoInput.files[0]) {
            videoError.textContent = 'Debes seleccionar un archivo de video.';
            valid = false;
        } else if (videoInput.files[0].size > 50 * 1024 * 1024) { // Límite de 50 MB
            videoError.textContent = 'El archivo no puede superar los 50 MB.';
            valid = false;
        } else {
            videoError.textContent = '';
        }

        if (!descripcionInput.value.trim() || /^[^a-zA-Z0-9]*$/.test(descripcionInput.value)) {
            descripcionError.textContent = 'La descripción no puede estar vacía ni contener solo símbolos.';
            valid = false;
        } else {
            descripcionError.textContent = '';
        }

        if (valid) {
            // Convertir el video a Base64
            const videoBase64 = await convertirArchivoABase64(videoInput.files[0]);

            nivelesData.push({
                curso_id: cursoId,
                nivel: index + 1,
                url_video: videoBase64,
                descripcion: descripcionInput.value.trim()
            });
        }
    }

    if (!valid || nivelesData.length === 0) {
        console.warn("No hay niveles válidos para registrar.");
        return;
    }

    // Enviar cada nivel de manera secuencial
    try {
        for (let i = 0; i < nivelesData.length; i++) {
            const nivel = nivelesData[i];
            await fetch('http://localhost/aprendi/api/nivelesController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(nivel)
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Error en la solicitud al registrar el nivel');
                }
                return response.json();
            }).then(responseData => {
                if (!responseData.success) {
                    throw new Error(responseData.message || 'Error desconocido al registrar el nivel');
                }
            });
        }

        // Mostrar el modal de éxito después de registrar todos los niveles
        mostrarModalExito();

        // Limpiar el formulario de niveles
        document.getElementById('registroCursoForm').reset();
        document.getElementById('nivelesContainer').innerHTML = '';

        // Ocultar el formulario de niveles y mostrar el de cursos
        document.getElementById('registroCursoForm').style.display = 'block';
        document.getElementById('nivelesForm').style.display = 'none';
    } catch (error) {
        console.error('Error al registrar niveles:', error);
        alert("Ocurrió un error al registrar los niveles.");
    }
}

function convertirArchivoABase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result); // Base64 del archivo
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

