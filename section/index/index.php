<?php
include("../../config/sessionVerif.php");

// Obtener el id del usuario de la sesión
$usuario_id = $_SESSION['usuario_id'];

// Hacer una solicitud a la API para obtener los datos del usuario por su ID
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/aprendi/api/usuariosController.php?id=" . $usuario_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

// Decodificar la respuesta de la API (asumiendo que devuelve un JSON)
$usuarioDatos = json_decode($response, true);

// Verificar si la API devolvió los datos correctamente
if (isset($usuarioDatos['status']) && $usuarioDatos['status'] === 'error') {
    echo "Error al obtener los datos del usuario: " . $usuarioDatos['message'];
} else {
    $correo = $usuarioDatos['correo'];
    $rol = $usuarioDatos['rol'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Cursos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">   
    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <!-- Barra de búsqueda -->
    <div class="container search-bar mt-4">
        <div class="row">
            <div class="col-md-8">
                <input type="text" class="form-control" placeholder="Buscar por nombre de curso...">
            </div>
            <div class="col-md-4 d-flex">
                <button class="btn btn-outline-secondary" id="searchBtn">
                    <i class="bi bi-search"></i> <!-- Icono de lupa -->
                </button>
                <button class="btn btn-outline-secondary ms-2" id="advancedSearchToggle">
                    <i class="bi bi-arrow-down"></i> <!-- Icono para abrir/cerrar -->
                </button>
            </div>
        </div>
    </div>

    <!-- Buscador avanzado -->
    <div class="advanced-search" id="advancedSearch">
        <h5>Búsqueda Avanzada</h5>
        <div class="row">
            <div class="col-md-6">
                <label for="startDate" class="form-label">Fecha de inicio:</label>
                <input type="date" id="startDate" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="endDate" class="form-label">Fecha de fin:</label>
                <input type="date" id="endDate" class="form-control">
            </div>
        </div>
        <button class="btn btn-primary mt-3">Buscar</button>
    </div>

    <!-- Lista de categorías fija -->
    <div class="category-list mt-5">
        <h5>Categorías</h5>
        <ul class="list-unstyled">
            <li><a href="#" class="text-dark">IT & Software</a></li>
            <li><a href="#" class="text-dark">Marketing</a></li>
            <li><a href="#" class="text-dark">Design</a></li>
        </ul>
    </div>

    <!-- Cursos destacados -->
    <div class="container mt-5">
        <div class="row">
            <!-- Ejemplos de cursos -->
            <div class="col-md-4">
                <div class="card course-card">
                    <img src="../Imagenes/Banner-desarrollo-de-software.png" class="card-img-top" alt="Curso 1">
                    <div class="card-body">
                        <h5 class="card-title">Curso de IT & Software</h5>
                        <p class="card-text">Aprende desde lo básico hasta avanzado.</p>
                        <p><strong>Costo:</strong> $50</p>
                        <a href="../cursos/curso.php" class="btn btn-green">Ver Curso</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card course-card">
                    <img src="../Imagenes/Marketing.jpg" class="card-img-top" alt="Curso 2">
                    <div class="card-body">
                        <h5 class="card-title">Curso de Marketing Digital</h5>
                        <p class="card-text">Conviértete en un experto en marketing online.</p>
                        <p><strong>Costo:</strong> $75</p>
                        <a href="../cursos/curso.php" class="btn btn-green">Ver Curso</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card course-card">
                    <img src="../Imagenes/Design.jpg" class="card-img-top" alt="Curso 3">
                    <div class="card-body">
                        <h5 class="card-title">Curso de Design Digital</h5>
                        <p class="card-text">Aprende las herramientas básicas para diseñar desde tu computadora.</p>
                        <p><strong>Costo:</strong> $35</p>
                        <a href="../cursos/curso.php" class="btn btn-green">Ver Curso</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Capturar los datos del usuario pasados por PHP
        const correo = "<?php echo $correo; ?>";
        const rol = "<?php echo $rol; ?>";
        // Mostrar en la consola usando JavaScript
        console.log("Usuario en sesión: " + correo);
        console.log("Rol del usuario: " + rol);
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
    .advanced-search {
        display: none;
        margin-top: 20px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
</style>