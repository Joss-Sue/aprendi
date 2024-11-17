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
    <title>Bloquear Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
    <style>
        /* Estilo personalizado para centrar el contenido */
        .container {
            margin-top: 50px;
        }
        .user-list {
            margin-top: 20px;
        }
        .user-item {
            font-size: 1.2rem; /* Tamaño de fuente más grande */
            margin: 10px 0;
            padding: 10px;
            border: 1px solid transparent; /* Borde inicial */
            transition: border 0.3s; /* Transición para el borde */
        }
        .user-item.blocked {
            border: 1px solid gray; /* Borde gris si está bloqueado */
            background-color: #f8d7da; /* Fondo claro para resaltar */
        }
    </style>
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <div class="container text-center">
        <h2>Página para Bloquear Usuario</h2>

        <!-- Buscador de usuarios -->
        <div class="mb-4">
            <input type="text" class="form-control" id="searchUser" placeholder="Buscar por nombre o correo...">
        </div>

        <!-- Lista de usuarios -->
        <div class="user-list">
            <h3>Usuarios Registrados</h3>
            <ul class="list-unstyled" id="usuariosContainer">
            </ul>
        </div>
    </div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
    <script src="../scriptJS/bloquearUsuario-val.js"></script>
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
