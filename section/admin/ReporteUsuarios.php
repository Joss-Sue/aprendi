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

$rol_requerido = "administrador"; // El rol que puede acceder a esta página

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
    <title>Reporte de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
</head>
<style>
    .container {
        margin-top: 50px;
    }
    .report-table {
        margin-top: 20px;
    }
    .report-table th, .report-table td {
        text-align: center;
    }
    .table-header-custom {
    background-color: #4db6ac !important; /* Color personalizado */
    color: white; /* Opcional: cambiar el color del texto para contraste */
}

</style>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <div class="container text-center">
        <h2>Reporte de Usuarios</h2>

        <!-- Selector de tipo de usuario -->
        <div class="mb-4">
            <select id="reporteTipo" class="form-select">
                <option value="INSTRUCTOR">Instructor</option>
                <option value="ESTUDIANTE">Estudiante</option>
            </select>
        </div>

        <!-- Buscador de usuarios -->
        <div class="mb-4">
            <input type="text" class="form-control" id="searchUser" placeholder="Buscar por nombre..." oninput="filterUsers()">
        </div>

        <!-- Tabla para mostrar reporte de instructores -->
        <div id="reporteInstructores" class="table-responsive" style="display: none;">
            <table class="table table-bordered">
                <thead class="table-header-custom">
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Fecha de Ingreso</th>
                        <th>Cursos Ofrecidos</th>
                        <th>Total de Ganancias</th>
                    </tr>
                </thead>
                <tbody id="tablaInstructores"></tbody>
            </table>
        </div>

        <!-- Tabla para mostrar reporte de estudiantes -->
        <div id="reporteEstudiantes" class="table-responsive" style="display: none;">
            <table class="table table-bordered">
                <thead class="table-header-custom">
                    <tr>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Fecha de Ingreso</th>
                        <th>Cursos Inscritos</th>
                        <th>% de Cursos Terminados</th>
                    </tr>
                </thead>
                <tbody id="tablaEstudiantes"></tbody>
            </table>
        </div>
    </div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>

    <script src="../scriptJS/reportesUsuarios-val.js"></script>
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