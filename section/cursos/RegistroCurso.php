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
    <title>Registrar Curso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <!-- Título -->
    <div class="container mt-5">
        <h1 class="text-center">Registrar Nuevo Curso</h1>
    </div>

    <!-- Formulario para registrar curso -->
    <div class="container mt-4">
        <form id="registroCursoForm" method="POST">
        <input type="hidden" id="usuarioId" value="<?php echo $usuario_id; ?>">
            <!-- Selector de Curso -->
            <div class="mb-3">
                <label for="cursoSelect" class="form-label">Selecciona un Curso</label>
                <select class="form-select" id="categoria" name="categoria">
                <option value="">Seleccione una categoría</option>
                </select>
                <span id="error-categoria" class="error-message"></span>
            </div>

            <div class="mb-3">
                <label for="courseName" class="form-label">Nombre del Curso</label>
                <input type="text" class="form-control" id="titulo" name="titulo" >
                <span class="error-message" id="error-titulo"></span>
            </div>

            <div class="mb-3">
                <label for="levels" class="form-label">Cantidad de Niveles</label>
                <input type="number" class="form-control" id="cantidad_niveles" name="cantidad_niveles" >
                <span class="error-message" id="error-cantidad_niveles"></span>
            </div>

            <div class="mb-3">
                <label for="cost" class="form-label">Costo por Curso Completo</label>
                <input type="number" class="form-control" id="costo_total" name="costo_total">
                <span class="error-message" id="error-costo_total"></span>
            </div>

            <div class="mb-3">
                <label for="costPerLevel" class="form-label">Costo por Nivel</label>
                <input type="number" class="form-control" id="costo_por_nivel" name="costo_por_nivel" >
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descripción del Curso</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" ></textarea>
                <span class="error-message" id="error-descripcion"></span>
            </div>

            <div class="mb-3">
                <label for="videoUpload" class="form-label">Subir Videos del Curso</label>
                <input class="form-control" type="file" id="videoUpload" name="videoUpload[]" multiple accept="video/*" >
            </div>
            
            <button type="submit" class="btn btn-green">Registrar Curso</button>
        </form>
        
        <div class="mb-3" id="nivelesForm" style="display: none;" >
                <h3>Agregar Niveles al Curso</h3>
                <label for="cursosSelect">Seleccionar Curso:</label>
                <select id="cursosSelect" class="form-control"></select>
                <br>
                <label for="cantidad_niveles_nuevo">Niveles del curso</label>
                <!-- <input type="number" id="cantidad_niveles_nuevo" class="form-control" min="1" placeholder="Cantidad de niveles"> -->
                <br>
                <div id="nivelesContainer"></div>
                <button class="btn btn-green" onclick="registrarNiveles()">Registrar Niveles</button>
            </div>
    </div>

    <!-- Modal de éxito con Bootstrap -->
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
    <script src="../scriptJS/registroCurso-val.js"></script>
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
