document.addEventListener('DOMContentLoaded', function () {
    const comboBox = document.getElementById('categoria');
    const cantidadNivelesInput = document.getElementById('cantidad_niveles');
    const costoTotalInput = document.getElementById('costo_total');
    const costoPorNivelInput = document.getElementById('costo_por_nivel');
    const tituloInput = document.getElementById('titulo');
    const descripcionInput = document.getElementById('descripcion');
    const formRegistroCurso = document.getElementById('registroCursoForm');

    // Captura el ID del usuario actual
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

            if (valid) {
                enviarFormularioCurso(usuarioId); // Pasar el ID del usuario
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

// Enviar formulario de curso a la API
function enviarFormularioCurso(usuarioId) {
    const formData = new FormData(document.getElementById('registroCursoForm'));
    const data = {
        titulo: formData.get('titulo'),
        descripcion: formData.get('descripcion'),
        costo: formData.get('costo_total'),
        instructor: usuarioId,
        categoria: formData.get('categoria')
    };

    fetch('http://localhost/aprendi/api/cursoController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la solicitud');
        }
        return response.json();
    })
    .then(data => {
        mostrarModalExito();
        document.getElementById('registroCursoForm').reset();
        document.getElementById('costo_por_nivel').value = '';
    })
    .catch(error => {
        console.error('Error al registrar el curso:', error);
        mostrarMensajeError('error-message', "Error al registrar el curso. Por favor, inténtalo de nuevo.");
    });
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
