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
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <div class="container text-center">
        <h2>Reporte de Usuarios</h2>

        <!-- Selector de tipo de usuario -->
        <div class="mb-4">
            <select id="userType" class="form-select" onchange="toggleReport()">
                <option value="">Seleccionar tipo de usuario</option>
                <option value="instructor">Instructores</option>
                <option value="estudiante">Estudiantes</option>
            </select>
        </div>

        <!-- Buscador de usuarios -->
        <div class="mb-4">
            <input type="text" class="form-control" id="searchUser" placeholder="Buscar por nombre..." oninput="filterUsers()">
        </div>

        <!-- Tabla de reportes -->
        <div class="report-table">
            <table class="table table-bordered table-hover" id="reportTable">
                <thead>
                    <tr id="tableHeader">
                        <!-- Encabezados se llenan dinámicamente -->
                    </tr>
                </thead>
                <tbody id="reportBody">
                    <!-- Cuerpo de la tabla se llena dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>

    <!-- Incluir el menú y el footer con JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const users = [
            { type: 'instructor', username: 'instructor1', name: 'Juan Pérez', joinDate: '2023-01-10', coursesOffered: 5, totalEarnings: 1500 },
            { type: 'instructor', username: 'instructor2', name: 'María Gómez', joinDate: '2023-02-15', coursesOffered: 3, totalEarnings: 900 },
            { type: 'instructor', username: 'instructor3', name: 'Carlos López', joinDate: '2023-03-20', coursesOffered: 8, totalEarnings: 2500 },
            { type: 'estudiante', username: 'student1', name: 'Laura Martínez', joinDate: '2023-01-12', coursesEnrolled: 4, coursesCompleted: 3 },
            { type: 'estudiante', username: 'student2', name: 'Andrés Torres', joinDate: '2023-04-25', coursesEnrolled: 5, coursesCompleted: 2 },
            { type: 'estudiante', username: 'student3', name: 'Ana Ruiz', joinDate: '2023-05-30', coursesEnrolled: 3, coursesCompleted: 1 },
        ];

        function toggleReport() {
            const userType = document.getElementById('userType').value;
            const reportBody = document.getElementById('reportBody');
            const tableHeader = document.getElementById('tableHeader');

            reportBody.innerHTML = '';
            tableHeader.innerHTML = '';

            if (userType === 'instructor') {
                tableHeader.innerHTML = `
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Fecha de Ingreso</th>
                    <th>Cursos Ofrecidos</th>
                    <th>Total de Ganancias</th>
                `;

                users.filter(user => user.type === 'instructor').forEach(user => {
                    reportBody.innerHTML += `
                        <tr>
                            <td>${user.username}</td>
                            <td>${user.name}</td>
                            <td>${user.joinDate}</td>
                            <td>${user.coursesOffered}</td>
                            <td>$${user.totalEarnings}</td>
                        </tr>
                    `;
                });
            } else if (userType === 'estudiante') {
                tableHeader.innerHTML = `
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Fecha de Ingreso</th>
                    <th>Cursos Inscritos</th>
                    <th>% de Cursos Terminados</th>
                `;

                users.filter(user => user.type === 'estudiante').forEach(user => {
                    const completionPercentage = ((user.coursesCompleted / user.coursesEnrolled) * 100).toFixed(2);
                    reportBody.innerHTML += `
                        <tr>
                            <td>${user.username}</td>
                            <td>${user.name}</td>
                            <td>${user.joinDate}</td>
                            <td>${user.coursesEnrolled}</td>
                            <td>${completionPercentage}%</td>
                        </tr>
                    `;
                });
            }
        }

        function filterUsers() {
            const input = document.getElementById('searchUser').value.toLowerCase();
            const userType = document.getElementById('userType').value;
            const reportBody = document.getElementById('reportBody');

            const filteredUsers = users.filter(user => user.type === userType && (user.name.toLowerCase().includes(input) || user.username.toLowerCase().includes(input)));

            reportBody.innerHTML = '';
            filteredUsers.forEach(user => {
                if (userType === 'instructor') {
                    reportBody.innerHTML += `
                        <tr>
                            <td>${user.username}</td>
                            <td>${user.name}</td>
                            <td>${user.joinDate}</td>
                            <td>${user.coursesOffered}</td>
                            <td>$${user.totalEarnings}</td>
                        </tr>
                    `;
                } else if (userType === 'estudiante') {
                    const completionPercentage = ((user.coursesCompleted / user.coursesEnrolled) * 100).toFixed(2);
                    reportBody.innerHTML += `
                        <tr>
                            <td>${user.username}</td>
                            <td>${user.name}</td>
                            <td>${user.joinDate}</td>
                            <td>${user.coursesEnrolled}</td>
                            <td>${completionPercentage}%</td>
                        </tr>
                    `;
                }
            });
        }
    </script>
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

            // Funcionalidad para mostrar/ocultar buscador avanzado
            document.getElementById('advancedSearchToggle').addEventListener('click', function() {
                const advancedSearch = document.getElementById('advancedSearch');
                advancedSearch.style.display = (advancedSearch.style.display === 'none' || advancedSearch.style.display === '') ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>
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
</style>