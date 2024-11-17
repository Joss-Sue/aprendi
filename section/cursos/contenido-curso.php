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
    <title>Contenido del Curso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
    <style>
        .container {
            display: flex;
            flex-wrap: nowrap;
            align-items: flex-start;
        }
        .video-container {
            flex: 3;
            padding-right: 20px;
            margin: 10px;
        }
        .levels-container {
            flex: 1;
            max-width: 900px;
            padding-left: 20px;
            border-left: 1px solid #ddd;
            max-height: 400px;
            overflow-y: auto;
        }
        .nivel-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .nivel-card:hover {
            background-color: #f0f0f0;
        }
        video {
            width: 100%;
            height: 500px;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        #nivelDescripcion {
            margin-top: 5px;
            font-size: 1.5rem;
            color: #666;
        }
        #cursoTitulo{
            margin-bottom: 2rem;
        }
        .niveles-videos{
            display: contents;
            justify-content: center;
        }
    </style>
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <button class="return btn btn-secondary m-3" onclick="history.back()">Regresar</button>
    <div class="container mt-2">
        <!-- Reproductor de video -->
         <div class="niveles-videos">
        <div class="video-container">
            <h2 id="cursoTitulo">[Título del Curso]</h2>
            <div id="videoContainer"></div>
            <p id="nivelDescripcion">Selecciona un nivel</p>
        </div>
        <!-- Lista de niveles -->
        <div class="levels-container">
            <h5>Niveles del Curso</h5>
            <ul id="nivelesContainer" class="list-group"></ul>
        </div>
        </div>
    </div>
    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
    <script>
        const estudianteId = "<?php echo $usuario_id; ?>";
    </script>
    <script src="../scriptJS/contenidoCurso-val.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
