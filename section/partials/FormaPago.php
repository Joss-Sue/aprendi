<?php
include("../../config/sessionVerif.php");
$usuario_id = $_SESSION['usuario_id'];
// Hacer una solicitud a la API 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/aprendi/api/usuariosController.php?id=" . $usuario_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

// Decodificar la respuesta de la API
$usuarioDatos = json_decode($response, true);

if (isset($usuarioDatos['status']) && $usuarioDatos['status'] === 'error') {
    header("Location: error.php");
    exit(); // Detener el script si hay un error
} else {
    $correo = $usuarioDatos['correo'];
    $rol = $usuarioDatos['rol'];
}
$cursoId = isset($_GET['curso_id']) ? $_GET['curso_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forma de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <!-- Título -->
    <div class="container mt-5">
        <h1 class="text-center">Seleccionar Forma de Pago</h1>
    </div>

    <!-- Formulario de Pago -->
    <div class="container mt-4">
        <form id="pagoForm" method="POST">
        <input type="hidden" id="cursoId" name="cursoId">
        <input type="hidden" id="precioCurso" name="precioCurso">

        <!-- Mostrar el nombre del curso que se está comprando -->
        <h3 id="nombreCurso" class="nombreCurso"></h3>
        <h3 id="precioCurso" class="precioCurso"></h3>
            <!-- Opciones de Pago -->
            <div class="mb-4">
                <h5>Elige tu método de pago:</h5>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard" value="creditCard" >
                    <label class="form-check-label" for="creditCard">
                        Tarjeta de Crédito/Débito
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="paypal" value="paypal">
                    <label class="form-check-label" for="paypal">
                        PayPal
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="bankTransfer" value="bankTransfer">
                    <label class="form-check-label" for="bankTransfer">
                        Transferencia Bancaria
                    </label>
                </div>
                <span class="error-message" id="error-metodo"></span>
            </div>

            <!-- Detalles de Tarjeta de Crédito -->
            <div id="creditCardInfo" class="payment-details">
                <h5>Detalles de Tarjeta de Crédito</h5>
                <div class="mb-3">
                    <label for="cardNumber" class="form-label">Número de Tarjeta</label>
                    <input type="text" class="form-control" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456">
                    <span class="error-message" id="error-numero"></span>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="expiryDate" class="form-label">Fecha de Expiración</label>
                        <input type="text" class="form-control" id="expiryDate" name="expiryDate" placeholder="MM/AA">
                        <span class="error-message" id="error-fecha"></span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cvv" class="form-label">CVV</label>
                        <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123">
                        <span class="error-message" id="error-cvv"></span>
                    </div>
                </div>
            </div>
            <!-- Información de PayPal -->
            <div id="paypalInfo" class="payment-details d-none">
                <h5>Pago con PayPal</h5>
                <p>Serás redirigido a PayPal para completar tu compra de forma segura.</p>
            </div>
            <!-- Información de Transferencia Bancaria -->
            <div id="bankTransferInfo" class="payment-details d-none">
                <h5>Pago con Transferencia Bancaria</h5>
                <p>Recibirás la información bancaria en tu correo para completar la transferencia.</p>
            </div>

            <!-- Botón de Enviar -->
            <div class="mt-4">
            <button id="completarPagoBtn" class="btn btn-green mt-4">Completar Pago</button>
            </div>
        </form>
        <div id="mensajeExito"></div>
    </div>

    <!-- Modal de éxito-->
    <div class="modal fade" id="modalExito" tabindex="-1" aria-labelledby="modalExitoLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalExitoLabel">Registro Exitoso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            Curso registrado con éxito.
        </div>
        <div class="modal-footer">
        <button id="cerrarModal" type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
        </div>
        </div>
        </div>
    </div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>

    <!-- Incluir el menú y el footer con JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const pagoForm = document.getElementById('pagoForm');
    const nombreCursoElement = document.getElementById('nombreCurso');
    const cursoIdInput = document.getElementById('cursoId');
    const precioCursoInput = document.getElementById('precioCurso');
    const cardNumber = document.getElementById('cardNumber');
    const expiryDate = document.getElementById('expiryDate');
    const cvv = document.getElementById('cvv');
    const usuarioId = "<?php echo $usuario_id; ?>";

    // Obtener el ID del curso desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    const cursoId = urlParams.get('id');

    if (!cursoId || !usuarioId) {
        alert("No se pudo obtener el ID del curso o del usuario.");
        return;
    }

    // Configurar el campo oculto con el curso ID
    cursoIdInput.value = cursoId;

    // Cargar los detalles del curso
    cargarCurso(cursoId);

    // Manejar el envío del formulario
    if (pagoForm) {
        pagoForm.addEventListener('submit', function (event) {
            event.preventDefault();
            limpiarMensajesError();

            if (validarFormulario()) {
                enviarFormularioPago(usuarioId, cursoId);
            }
        });
    }
});

// Función para cargar los detalles del curso
function cargarCurso(cursoId) {
    const nombreCursoElement = document.querySelector('#nombreCurso');
    const precioCursoInput = document.querySelector('#precioCurso');

    fetch(`http://localhost/aprendi/api/cursoController.php?id=${cursoId}`, {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(curso => {
        if (curso && curso.id) {
            nombreCursoElement.textContent = `Comprando: ${curso.titulo}`;
            precioCursoInput.value = curso.costo;
        } else {
            console.error('No se encontraron datos válidos del curso.');
        }
    })
    .catch(error => console.error('Error al obtener datos del curso:', error));
}

// Función para validar el formulario
function validarFormulario() {
    const metodoPagoElement = document.querySelector('input[name="paymentMethod"]:checked');
    const cardNumber = document.getElementById('cardNumber');
    const expiryDate = document.getElementById('expiryDate');
    const cvv = document.getElementById('cvv');

    let valid = true;

    if (!metodoPagoElement) {
        mostrarMensajeError('error-metodo', 'Debe seleccionar un método de pago.');
        valid = false;
    }
    if (!cardNumber.value || /^[^a-zA-Z0-9]*$/.test(cardNumber.value)) {
        mostrarMensajeError('error-numero', 'El número de tarjeta no puede estar vacío o ser solo símbolos.');
        valid = false;
    }
    if (!expiryDate.value || /^[^a-zA-Z0-9]*$/.test(expiryDate.value)) {
        mostrarMensajeError('error-fecha', 'La fecha de expiración no puede estar vacía o ser solo símbolos.');
        valid = false;
    }
    if (!cvv.value || /^[^a-zA-Z0-9]*$/.test(cvv.value)) {
        mostrarMensajeError('error-cvv', 'El CVV no puede estar vacío o ser solo símbolos.');
        valid = false;
    }

    return valid;
}

// Función para enviar el formulario de pago
function enviarFormularioPago(usuarioId, cursoId) {
    const metodoPagoElement = document.querySelector('input[name="paymentMethod"]:checked');
    const precioCursoInput = document.getElementById('precioCurso');

    const data = {
        curso_id: cursoId,
        estudiante_id: usuarioId,
        tipo_pago: metodoPagoElement.value,
        precio_pagado: parseFloat(precioCursoInput.value)
    };

    fetch('http://localhost/aprendi/api/inscripcionesController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(responseData => {
        if (responseData.success) {
            mostrarModalExito();
        } else {
            alert("Error al inscribir: " + responseData.message);
        }
    })
    .catch(error => {
        console.error('Error al inscribir al usuario:', error);
    });
}

// Función para mostrar mensajes de error
function mostrarMensajeError(elementId, mensaje) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.innerText = mensaje;
        errorElement.style.color = 'red';
    }
}
// Función para limpiar mensajes de error
function limpiarMensajesError() {
    document.querySelectorAll('.error-message').forEach(error => error.innerText = '');
}
// Modal de éxito
function mostrarModalExito() {
    const modal = new bootstrap.Modal(document.getElementById('modalExito'));
    modal.show();

    document.getElementById('cerrarModal').addEventListener('click', function () {
                window.location.href = "../cursos/mis-cursos.php";
        document.getElementById('registroCursoForm').reset();
        document.getElementById('costo_por_nivel').value = '';

    });
}
</script>
</body>
</html>
