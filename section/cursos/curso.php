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

// Obtener el ID del curso de la URL
$curso_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($curso_id) {
    // Llamar a la API para obtener los detalles del curso
    $url = "http://localhost/aprendi/api/cursoController.php?id=" . $curso_id;
    $response = file_get_contents($url);
    $curso = json_decode($response, true);

    if ($curso) {
        // Mostrar los detalles del curso
        echo "<h1>" . htmlspecialchars($curso['titulo']) . "</h1>";
        echo "<p>" . htmlspecialchars($curso['descripcion']) . "</p>";
        echo "<p><strong>Costo:</strong> $" . htmlspecialchars($curso['costo_total']) . "</p>";
        // Añadir cualquier otro detalle necesario
    } else {
        echo "<p>No se pudo cargar el curso.</p>";
    }
} else {
    echo "<p>ID de curso no especificado.</p>";
}

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
        background-image: url('Imagenes/It&Software(curso).jpg'); /* Imagen de fondo */
        background-size: cover; /* Cubrir todo el área */
        background-position: center; /* Centrar la imagen */
        border-radius: 8px; /* Bordes redondeados */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Sombra para dar profundidad */
        overflow: hidden; /* Ocultar contenido que se desborda */
    }
    .video-container {
        position: absolute; /* Posicionar el video de manera absoluta */
        top: 50%; /* Centrar verticalmente */
        left: 35%; /* Centrar horizontalmente */
        transform: translate(-50%, -50%); /* Ajustar el centrado */
        transition: transform 0.5s ease; /* Transición para el efecto de movimiento */
    }
    .video-container video {
        width: 225%;
        height: 225%;
        border-radius: 8px; /* Bordes redondeados para el video */
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
    .btn-green {
        background-color: #28a745; /* Color verde para los botones */
        color: white;
    }
    .btn-green:hover {
        background-color: #218838; /* Color verde oscuro al pasar el mouse */
    }
    .return {
        margin: 10px 0;
        font-size: 18px;
    }
    /* Estilo para estrellas */
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        margin-top: 10px;
        font-size: 2em;
        color: #ffd700; /* Color dorado para las estrellas */
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
    .selected {
        color: #ffc700; /* Color amarillo oscuro para la selección */
    }
</style>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <div class="container mt-5">
        <button class="return btn btn-outline-secondary" onclick="history.back()">Regresar</button>
        
        <div class="course-header">
            <h1>Curso de IT & Software</h1>
            <p>Niveles: 10 videos</p>
            <p>Costo: $49.99</p>
        </div>

        <div class="video-section">
            <div id="videoContainer" class="video-container">
                <video id="mainVideo" controls>
                    <source src="ruta/a/tu/video1.mp4" type="video/mp4">
                    Tu navegador no soporta la etiqueta de video.
                </video>
            </div>
        </div>

        <div class="thumbnail-container">
            <img src="ruta/a/tu/video1_thumbnail.jpg" alt="Video 1" class="thumbnail" onclick="changeVideo('ruta/a/tu/video1.mp4')">
            <img src="ruta/a/tu/video2_thumbnail.jpg" alt="Video 2" class="thumbnail" onclick="changeVideo('ruta/a/tu/video2.mp4')">
            <img src="ruta/a/tu/video3_thumbnail.jpg" alt="Video 3" class="thumbnail" onclick="changeVideo('ruta/a/tu/video3.mp4')">
            <img src="ruta/a/tu/video4_thumbnail.jpg" alt="Video 4" class="thumbnail" onclick="changeVideo('ruta/a/tu/video4.mp4')">
        </div>

        <div class="comprar-curso mt-4">
            <h4>Comprar Curso</h4>
            <a href="../partials/FormaPago.php" class="btn btn-green">Comprar</a>
        </div>

        <div class="mt-4 text-center"> <!-- Centrado -->
            <h4>Valoración del curso</h4>
            <div class="rating" id="rating">
                <input type="radio" name="star" id="star5" value="5"><label for="star5">★</label>
                <input type="radio" name="star" id="star4" value="4"><label for="star4">★</label>
                <input type="radio" name="star" id="star3" value="3"><label for="star3">★</label>
                <input type="radio" name="star" id="star2" value="2"><label for="star2">★</label>
                <input type="radio" name="star" id="star1" value="1"><label for="star1">★</label>
            </div>
            <button class="btn btn-green mt-2" onclick="submitRating()">Emitir Valoración</button>
        </div>

        <!-- Sección de Comentarios -->
        <div class="comment-section mt-4">
            <h4>Comentarios</h4>
            <div class="card mt-3">
                <div class="card-body">
                    <strong>Usuario1</strong> - <small>Fecha: 2024-09-21</small>
                    <p>Excelente curso, lo recomiendo.</p>
                    <?php if ($rol == 'administrador'): ?>
                    <button class="btn btn-danger btn-sm" onclick="deleteComment(this)">Eliminar</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <strong>Usuario2</strong> - <small>Fecha: 2024-09-20</small>
                    <p>El curso fue útil, pero algo corto.</p>
                    <?php if ($rol == 'administrador'): ?>
                    <button class="btn btn-danger btn-sm" onclick="deleteComment(this)">Eliminar</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <strong>Usuario3</strong> - <small>Fecha: 2024-07-03</small>
                    <p><em>Comentario eliminado por administrador.</em></p>
                </div>
            </div>

            <!-- Formulario para añadir un nuevo comentario -->
            <div id="formulario-com" class="mt-4">
                <h4>Deja tu comentario</h4>
                <form id="commentForm">
                    <div class="mb-3">
                        <label for="commentText" class="form-label">Comentario</label>
                        <textarea class="form-control" id="commentText" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-green">Enviar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
    
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

            // Funcionalidad para mostrar/ocultar buscador avanzado
            document.getElementById('advancedSearchToggle').addEventListener('click', function() {
                const advancedSearch = document.getElementById('advancedSearch');
                advancedSearch.style.display = (advancedSearch.style.display === 'none' || advancedSearch.style.display === '') ? 'block' : 'none';
            });
        });

        // Cambiar el video principal
        function changeVideo(videoSrc) {
            const mainVideo = document.getElementById('mainVideo');
            mainVideo.src = videoSrc;
            mainVideo.play(); // Reproducir automáticamente el nuevo video
        }

        // Enviar valoración
        function submitRating() {
            const rating = document.querySelector('input[name="star"]:checked');
            if (rating) {
                alert('Gracias por su valoración de ' + rating.value + ' estrellas');
            } else {
                alert('Por favor, selecciona una valoración');
            }
        }

        // Eliminar comentario
        function deleteComment(button) {
            const cardBody = button.parentNode;
            cardBody.innerHTML = `
                <strong>Comentario eliminado por administrador.</strong>
            `;
        }

        document.getElementById('commentForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const commentText = document.getElementById('commentText').value;

            // Crear un nuevo comentario
            const newComment = document.createElement('div');
            newComment.classList.add('card', 'mt-3');
            newComment.innerHTML = `
                <div class="card-body">
                    <strong>Usuario anónimo</strong> - <small>Fecha: ${new Date().toLocaleDateString()}</small>
                    <p>${commentText}</p>
                    <button class="btn btn-danger btn-sm" onclick="deleteComment(this)">Eliminar</button>
                </div>
            `;

            // Insertar el comentario al principio de la sección de comentarios
            const commentSection = document.querySelector('#formulario-com');
            commentSection.insertBefore(newComment, commentSection.firstChild);

            // Limpiar el formulario
            document.getElementById('commentForm').reset();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    // Seleccionar todos los botones "Ver Curso"
    const viewCourseButtons = document.querySelectorAll('.view-course-btn');

    viewCourseButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Evita el comportamiento del enlace

            const cursoId = this.getAttribute('data-id'); // Obtiene el ID del curso
            if (cursoId) {
                // Redirige a curso.php con el ID del curso
                window.location.href = `curso.php?id=${cursoId}`;
            } else {
                console.error('ID de curso no encontrado.');
            }
        });
    });
});

    </script>
</body>
</html>
