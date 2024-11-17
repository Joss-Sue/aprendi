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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
    <title>Registro de Categorías</title>
    <style>
        .container {
            margin-top: 30px;
        }
        .categorias-registradas {
            max-width: 300px;
            margin-right: 30px;
        }
        .formulario-categoria {
            flex-grow: 1;
        }
    </style>
</head>
<body>
        <!-- Contenedor del Menú -->
        <div id="menu-container"></div>

    <div class="container d-flex">
        <!-- Categorías registradas -->
        <div class="card categorias-registradas">
            <div class="card-body">
                <h5 class="card-title">Categorías Registradas</h5>
                <ul class="list-group" id="listaCategorias">
                </ul>
            </div>
        </div>

        <!-- Formulario de categoría -->
        <div class="card formulario-categoria">
            <div class="card-body">
                <h5 class="card-title" id="formularioTitulo">Registrar Nueva Categoría</h5>
                <form id="formCategoria">
                    <div class="mb-3">
                        <label for="nombreCategoria" class="form-label">Nombre de la Categoría</label>
                        <input type="text" class="form-control" id="nombreCategoria" placeholder="Nombre de la categoría">
                    </div>
                    <div class="mb-3">
                        <label for="descripcionCategoria" class="form-label">Descripción de la Categoría</label>
                        <textarea class="form-control" id="descripcionCategoria" rows="3" placeholder="Descripción de la categoría"></textarea>
                    </div>
                    <button type="button" class="btn btn-green" id="btnGuardar" onclick="guardarCategoria()">Agregar Categoría</button>
                </form>
            </div>
        </div>
    </div>

    <div id="footer-container"></div>
    <script src="../scriptJS/registroCategoria-val.js"></script>
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
