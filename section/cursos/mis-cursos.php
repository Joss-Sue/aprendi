<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Cursos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <div class="container mt-5">
        <h3 class="text-center">Mis Cursos</h3>
        <div class="row mt-4">
            <!-- Ejemplo de curso 1 -->
            <div class="col-md-4">
                <div class="card course-card">
                    <img src="../Imagenes/It&Software(curso).jpg" class="card-img-top" alt="Curso 1">
                    <div class="card-body">
                        <h5 class="card-title">Curso de IT & Software</h5>
                        <p class="card-text">Aprende desde lo básico hasta avanzado en el mundo del software.</p>
                        <a href="../cursos/contenido-curso.php" class="btn btn-green">Empezar</a>
                    </div>
                </div>
            </div>
            <!-- Ejemplo de curso 2 -->
            <div class="col-md-4">
                <div class="card course-card">
                    <img src="../Imagenes/Marketing.jpg" class="card-img-top" alt="Curso 2">
                    <div class="card-body">
                        <h5 class="card-title">Curso de Marketing Digital</h5>
                        <p class="card-text">Conviértete en un experto en marketing digital.</p>
                        <a href="../cursos/contenido-curso.php" class="btn btn-green">Empezar</a>
                    </div>
                </div>
            </div>
            <!-- Ejemplo de curso 3 -->
            <div class="col-md-4">
                <div class="card course-card">
                    <img src="../Imagenes/Design.jpg" class="card-img-top" alt="Curso 3">
                    <div class="card-body">
                        <h5 class="card-title">Curso de Design Digital</h5>
                        <p class="card-text">Aprende las herramientas básicas para diseñar desde tu computadora.</p>
                        <a href="../cursos/contenido-curso.php" class="btn btn-green">Empezar</a>
                    </div>
                </div>
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
    </script>
</body>
</html>
