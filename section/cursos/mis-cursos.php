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
    <title>Mis Cursos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
</head>
<style>
        .curso-card {
        width: 300px;
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        margin: 20px;
        display: inline-block;
        vertical-align: top;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .curso-card:hover {
        transform: translateY(-5px);
    }

    .curso-card-content {
        text-align: center;
        padding: 20px;
    }

    .curso-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .curso-info h5 {
        font-size: 1.25rem;
        margin: 10px 0;
    }

    .progreso {
        background-color: #e0e0e0;
        border-radius: 5px;
        height: 10px;
        margin: 10px 0;
    }

    .progreso-bar {
        background-color: #4caf50;
        height: 100%;
        border-radius: 5px;
    }

</style>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <div class="container mt-5" >
        <h3 class="text-center">Mis Cursos</h3>
        <div class="container mt-5" id="cursosContainer">
            <!-- Aquí se mostrarán los cursos inscritos -->
        </div>
    </div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
    <script>const usuarioId = "<?php echo $usuario_id; ?>";</script>
    <script src="../scriptJS/misCursos-val.js"></script>
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
