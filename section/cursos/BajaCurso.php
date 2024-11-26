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

$rol_requerido = "instructor"; // El rol que puede acceder a esta página

if ($rol !== $rol_requerido) {
    header("location:../index/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Cursos - Baja de Cursos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
</head>
<style>
        .modal-content {
        display: flex;
        flex-direction: column;
        height: auto;
    }

    .modal-body {
        display: flex;
        flex-direction: row;
        gap: 20px;
        overflow-y: auto; /* Permitir scroll si el contenido es grande */
    }

    .w-50 {
        width: 50%;
    }

</style>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <!-- Cursos inscritos -->
    <div class="container mt-4">
    <h2>Mis Cursos</h2>
    <div class="row">
            <div class="col-md-4">   
            </div>
    </div>
</div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>

    <div class="modal fade" id="editarCursoModal" tabindex="-1" aria-labelledby="editarCursoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Cambiado a modal-xl para más espacio -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarCursoModalLabel">Editar Curso y Niveles</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex">
                <!-- Columna izquierda: Formulario del curso -->
                <div class="w-50 pe-3 border-end">
                    <form id="editarCursoForm">
                        <h5>Datos del Curso</h5>
                        <div class="mb-3">
                            <label for="cursoTitulo" class="form-label">Título</label>
                            <input type="text" id="cursoTitulo" class="form-control" name="titulo">
                            <small id="errorCursoTitulo" class="text-danger"></small>
                        </div>
                        <div class="mb-3">
                            <label for="cursoDescripcion" class="form-label">Descripción</label>
                            <textarea id="cursoDescripcion" class="form-control" name="descripcion"></textarea>
                            <small id="errorCursoDescripcion" class="text-danger"></small>
                        </div>
                        <div class="mb-3">
                            <label for="cursoCosto" class="form-label">Costo</label>
                            <input type="number" id="cursoCosto" class="form-control" name="costo">
                            <small id="errorCursoCosto" class="text-danger"></small>
                        </div>
                        <div class="mb-3">
                            <label for="cursoCategoria" class="form-label">Categoría</label>
                            <select id="cursoCategoria" class="form-select" name="categoria">
                                <!-- Opciones cargadas dinámicamente -->
                            </select>
                            <small id="errorCursoCategoria" class="text-danger"></small>
                        </div>
                        <div class="mb-3">
                            <label for="cursoImagen" class="form-label">Imagen</label>
                            <input type="file" id="cursoImagen" class="form-control" name="imagen">
                        </div>
                        <button type="button" id="btnGuardarCurso" class="btn btn-green mt-2 w-100" onclick="guardarEdicionCurso()">Guardar Cambios del Curso</button>
                    </form>
                </div>

                <!-- Columna derecha: Formulario de los niveles -->
                <div class="w-50 ps-3">
                    <h5>Niveles del Curso</h5>
                    <div id="nivelesContainer"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verVideoModal" tabindex="-1" aria-labelledby="verVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verVideoModalLabel">Ver Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <video controls style="width: 100%; height: auto;"></video>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Éxito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="successModalMessage">Operación realizada con éxito.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmarBajaModal" tabindex="-1" aria-labelledby="confirmarBajaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmarBajaLabel">Confirmar Baja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas dar de baja este curso? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarBaja">Dar de Baja</button>
            </div>
        </div>
    </div>
</div>


    <script>
        const usuarioId = "<?php echo $_SESSION['usuario_id']; ?>";
    </script>
    <script src="../scriptJS/bajacurso-val.js"></script>
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
</body>
</html>
