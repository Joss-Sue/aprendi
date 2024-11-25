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
error_log("Este es un mensaje de prueba para confirmar los logs.");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curso - Detalles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
</head>
<style>
    body {
        margin: 0; /* Eliminar margen */
    }
    .course-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .video-section {
        position: relative;
        padding: 20px; /* Espaciado interno */
        height: 400px; /* Ajustar altura según sea necesario */
        background-size: cover; /* Cubrir todo el área */
        background-position: center; /* Centrar la imagen */
        border-radius: 8px; /* Bordes redondeados */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Sombra para dar profundidad */
        overflow: hidden; /* Ocultar contenido que se desborda */
    }
    .video-container img {
    width: 100%; /* Ocupa el 100% del ancho del contenedor */
    height: 300px; /* Fija una altura consistente */
    object-fit: cover; /* Recorta y ajusta la imagen para llenar el área */
    object-position: center; /* Centra la imagen dentro del contenedor */
    border-radius: 8px; /* Bordes redondeados */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Agrega una sombra */
    }

    .thumbnail-container {
        display: flex;
        justify-content: center; /* Centrar miniaturas */
        margin-top: 10px; /* Margen superior */
    }
    .thumbnail {
        width: 80px; /* Ancho de las miniaturas */
        height: 45px; /* Altura de las miniaturas */
        margin: 0 5px; /* Espaciado entre miniaturas */
        cursor: pointer; /* Cambiar el cursor al pasar el mouse */
        border: 2px solid transparent; /* Bordes transparentes por defecto */
        border-radius: 5px; /* Bordes redondeados */
    }
    .thumbnail:hover {
        border: 2px solid #007bff; /* Bordes azules al pasar el mouse */
    }
    .comprar-curso, .comment-section {
        text-align: center; /* Centrar texto */
    }

    /* Estilo para estrellas */
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        margin-top: 10px;
        font-size: 2em;
        color: #e9decf; /* Color dorado para las estrellas */
    }
    .rating input {
        display: none;
    }
    .rating label {
        cursor: pointer;
    }
    .rating label:hover,
    .rating input:checked ~ label:hover,
    .rating label:hover ~ label {
        color: #ffc700; /* Color amarillo oscuro en hover */
    }
    .rating input[type="radio"] {
    display: none; /* Ocultar los botones de radio */
    }

    .rating input[type="radio"]:checked ~ label {
        color: #ffc700;
    }

    /*Niveles*/
    .niveles-header {
        display: inline-block;
        font-size: 1.5rem;
        padding: 10px 20px;
        color: #ffffff;
        background-color: #4db6ac;
        border-radius: 25px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        text-align: center;
        margin-bottom: 20px;
    }

    .niveles-header:hover {
        background-color: #2e7b73;
    }

    .niveles-list {
        margin-top: 10px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        background-color: #f9f9f9;
        width: 100%;
    }

    .list-group-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        background-color: #f8f9fa;
    }

    .niveles {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 5px;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    .btn{
        margin-top: 0%;
        margin-bottom: 0%;
    }
        .ver-mas {
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
    }

    .ver-mas:hover {
        text-decoration: underline;
    }

</style>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <div class="container mt-5">
        <button class="return btn btn-outline-secondary" onclick="history.back()">Regresar</button>
        
    <div class="course-header text-center">
        <h1></h1>
        <p></p>
        <p></p>
    </div>
        <!-- Contenedor para los niveles -->
        <div class="niveles mt-4"></div>

        <div class="video-section">
            <!-- La imagen del curso se cargará aquí dinámicamente -->
            <div id="videoContainer" class="video-container">
                <img src="" alt="Imagen del curso" class="img-fluid rounded" style="max-width: 100%; height: auto;">
            </div>
        </div>


        <div class="comprar-curso mt-4">
            <h4>Comprar Curso Completo</h4>
            <?php if ($rol == 'estudiante'): ?>
            <a class="btn btn-green btnCompra">Comprar</a>
            <?php endif; ?>
        </div>

        <!-- Sección de Valoracion -->
        <div class="comment-section mt-4">
            <h4>Comentarios</h4>
            <div  id="comentariosContainer"></div>
        </div>
            <!-- Formulario para añadir un nuevo comentario -->
            <div id="commentSection" class="mt-4 text-center">
                    <div class="mt-4 text-center">
                        <h4>Valoración del curso</h4>
                        <div class="rating" id="rating">
                            <input type="radio" name="star" id="star5" value="5"><label for="star5">★</label>
                            <input type="radio" name="star" id="star4" value="4"><label for="star4">★</label>
                            <input type="radio" name="star" id="star3" value="3"><label for="star3">★</label>
                            <input type="radio" name="star" id="star2" value="2"><label for="star2">★</label>
                            <input type="radio" name="star" id="star1" value="1"><label for="star1">★</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" id="comment" rows="3"></textarea>
                        <p class="error-comentario"></p>
                    </div>
                    <button type="submit" class="btn btn-green" id="submitComment">Enviar</button>
            </div>
        </div>
    </div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
    <script>
                const estudianteId = <?php echo json_encode($usuario_id); ?>;
                const usuarioRol = <?php echo json_encode($rol); ?>; // "instructor" o "estudiante"
    </script>
    <!-- <script src="../scriptJS/valoracionCurso-val.js"></script> -->
    <script src="../scriptJS/detallesCurso-val.js"></script>
    <script src="../scriptJS/inscribirCurso-val.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script> -->
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
