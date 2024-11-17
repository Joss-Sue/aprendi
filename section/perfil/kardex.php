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

$rol_requerido = "estudiante"; // El rol que puede acceder a esta página

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
    <title>Kardex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <button class="return" onclick="history.back()">Regresar</button>
<div class="container mt-5">
        <h3 class="text-center">Kardex de Cursos</h3>

        <!-- Filtros -->
        <form id="filtroForm">
        <div class="filter-container mt-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="dateRange" class="form-label">Rango de fechas de inscripción:</label>
                    <input type="date" id="startDate" class="form-control">
                    <input type="date" id="endDate" class="form-control mt-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="category" class="form-label">Categoría:</label>
                    <select class="form-select" id="category" name="category">
                    <option value=""></option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mostrar:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="completedCourses">
                        <label class="form-check-label" for="completedCourses">Solo cursos terminados</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="activeCourses">
                        <label class="form-check-label" for="activeCourses">Solo cursos activos</label>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <button id="clearFilters" class="btn btn-green">Limpiar</button>
        <!-- Tabla Kardex -->
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Nombre del curso</th>
                    <th>Progreso</th>
                    <th>Fecha de inscripción</th>
                    <th>Último acceso</th>
                    <th>Fecha de terminación</th>
                    <th>Estado</th>
                    <th>Certificado</th>
                </tr>
            </thead>
            <tbody id="kardexContainer">
            </tbody>
        </table>

</div>
    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
    <!-- <button class="btn btn-green" onclick="descargarCertificado('Curso de Marketing Digital')">Descargar</button> -->
    <script>const estudianteId = "<?php echo $usuario_id; ?>";</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../scriptJS/kardex-val.js"></script>
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
