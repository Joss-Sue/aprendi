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
            <input type="text" class="form-control" id="searchUser" placeholder="Buscar por nombre o correo..." oninput="searchUser()">
        </div>

        <!-- Lista de usuarios -->
        <div class="user-list">
            <h3>Usuarios Registrados</h3>
            <ul class="list-unstyled" id="userList">
                <li class="user-item" id="user1">
                    Juan Pérez (juan.perez@example.com)
                    <button class="btn btn-danger btn-sm float-end" onclick="toggleUser('user1', 'Juan Pérez')">Bloquear</button>
                    <button class="btn btn-success btn-sm float-end d-none" onclick="toggleUser('user1', 'Juan Pérez')">Desbloquear</button>
                </li>
                <li class="user-item" id="user2">
                    María Gómez (maria.gomez@example.com)
                    <button class="btn btn-danger btn-sm float-end" onclick="toggleUser('user2', 'María Gómez')">Bloquear</button>
                    <button class="btn btn-success btn-sm float-end d-none" onclick="toggleUser('user2', 'María Gómez')">Desbloquear</button>
                </li>
                <li class="user-item" id="user3">
                    Carlos López (carlos.lopez@example.com)
                    <button class="btn btn-danger btn-sm float-end" onclick="toggleUser('user3', 'Carlos López')">Bloquear</button>
                    <button class="btn btn-success btn-sm float-end d-none" onclick="toggleUser('user3', 'Carlos López')">Desbloquear</button>
                </li>
                <li class="user-item" id="user4">
                    Laura Martínez (laura.martinez@example.com)
                    <button class="btn btn-danger btn-sm float-end" onclick="toggleUser('user4', 'Laura Martínez')">Bloquear</button>
                    <button class="btn btn-success btn-sm float-end d-none" onclick="toggleUser('user4', 'Laura Martínez')">Desbloquear</button>
                </li>
                <li class="user-item" id="user5">
                    Andrés Torres (andres.torres@example.com)
                    <button class="btn btn-danger btn-sm float-end" onclick="toggleUser('user5', 'Andrés Torres')">Bloquear</button>
                    <button class="btn btn-success btn-sm float-end d-none" onclick="toggleUser('user5', 'Andrés Torres')">Desbloquear</button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>

    <!-- Incluir el menú y el footer con JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleUser(userId, username) {
            const userItem = document.getElementById(userId);
            const blockButton = userItem.querySelector('.btn-danger');
            const unblockButton = userItem.querySelector('.btn-success');

            if (userItem.classList.contains('blocked')) {
                // Desbloquear usuario
                userItem.classList.remove('blocked');
                alert(username + " ha sido desbloqueado.");
                blockButton.classList.remove('d-none');
                unblockButton.classList.add('d-none');
            } else {
                // Bloquear usuario
                userItem.classList.add('blocked');
                alert(username + " ha sido bloqueado.");
                blockButton.classList.add('d-none');
                unblockButton.classList.remove('d-none');
            }
        }

        function searchUser() {
            const input = document.getElementById('searchUser').value.toLowerCase();
            const userItems = document.querySelectorAll('.user-item');

            userItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(input) ? '' : 'none';
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
