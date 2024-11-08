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

<style>
    .advanced-search {
        display: none;
        margin-top: 20px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    .course-card {
        border: 1px solid #ddd;
        border-radius: 5px;
        overflow: hidden;
    }
    /* Estilo para el contenedor de categorías */
    .category-list {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .category-list h5 {
        margin-bottom: 15px;
    }
    .category-list ul {
        padding-left: 0;
    }
    .category-list ul li a {
        display: block;
        padding: 5px 0;
        color: #343a40;
        text-decoration: none;
    }
    .category-list ul li a:hover {
        color: #0d6efd;
    }
</style>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>


    <!-- Contenedor principal -->
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Lista de categorías en la columna izquierda -->
            <div class="col-md-3">
                <div class="category-list">
                    <h5>Categorías</h5>
                    <ul class="list-unstyled" id="category-list">
                        <!-- Las categorías se cargarán dinámicamente aquí -->
                    </ul>
                </div>
            </div>

            <!-- Contenido principal (barra de búsqueda, cursos, etc.) -->
            <div class="col-md-9">
                <!-- Barra de búsqueda -->
                <div class="search-bar mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar por nombre de curso...">
                        <button class="btn btn-outline-secondary" id="searchBtn">
                            <i class="bi bi-search"></i>
                        </button>
                        <button class="btn btn-outline-secondary" id="advancedSearchToggle">
                            <i class="bi bi-arrow-down"></i>
                        </button>
                    </div>
                </div>

                <!-- Buscador avanzado -->
                <div class="advanced-search mb-4" id="advancedSearch">
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

                <!-- Contenedor de cursos -->
                <div id="courses-container" class="row">
                    <!-- Aquí se cargarán los cursos dinámicamente -->
                </div>
            </div>
        </div>
    </div>


    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar menú y footer
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

            // Mostrar/ocultar buscador avanzado
            document.getElementById('advancedSearchToggle').addEventListener('click', function() {
                const advancedSearch = document.getElementById('advancedSearch');
                advancedSearch.style.display = (advancedSearch.style.display === 'none' || advancedSearch.style.display === '') ? 'block' : 'none';
            });

            // Cargar categorías y cursos
            cargarCategorias();
        });

        // Cargar categorías dinámicamente
        function cargarCategorias() {
            fetch('http://localhost/aprendi/api/categoriaController.php')
                .then(response => response.json())
                .then(data => {
                    const categoryMenu = document.getElementById('category-list');
                    categoryMenu.innerHTML = ''; // Limpiar el menú antes de agregar categorías

                    data.forEach(categoria => {
                        const li = document.createElement('li');
                        li.innerHTML = `<a href="#" class="text-dark">${categoria.nombre}</a>`;
                        categoryMenu.appendChild(li);
                    });
                })
                .catch(error => console.error('Error al cargar las categorías:', error));
        }
    </script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    fetch('http://localhost/aprendi/api/cursoController.php?pagina=1')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar los cursos');
            }
            return response.json();
        })
        .then(data => {
            const container = document.getElementById('courses-container');
            container.innerHTML = ''; // Limpiar el contenedor
            data.forEach(curso => {
                const courseCard = document.createElement('div');
                courseCard.classList.add('col-md-4');
                courseCard.innerHTML = `
                    <div class="card course-card">
                        <div class="card-img-top" style="background-color: #ccc; height: 200px; display: flex; align-items: center; justify-content: center;">
                            <span style="color: #555;">Imagen no disponible</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">${curso.titulo}</h5>
                            <p class="card-text">${curso.descripcion}</p>
                            <p><strong>Costo:</strong> $${curso.costo}</p>
                            <a href="../cursos/curso.php?id=${curso.id}" class="btn btn-green">Ver Curso</a>
                        </div>
                    </div>
                `;
                container.appendChild(courseCard);
            });
        })
        .catch(error => {
            console.error('Error al cargar los cursos:', error);
        });
});
</script>
</body>
</html>