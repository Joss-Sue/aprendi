<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Curso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <!-- Título -->
    <div class="container mt-5">
        <h1 class="text-center">Registrar Nuevo Curso</h1>
    </div>

    <!-- Formulario para registrar curso -->
    <div class="container mt-4">
        <form action="procesar_registro_curso.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="courseName" class="form-label">Nombre del Curso</label>
                <input type="text" class="form-control" id="courseName" name="courseName" required>
            </div>

            <div class="mb-3">
                <label for="levels" class="form-label">Cantidad de Niveles</label>
                <input type="number" class="form-control" id="levels" name="levels" required>
            </div>

            <div class="mb-3">
                <label for="cost" class="form-label">Costo por Curso Completo</label>
                <input type="number" class="form-control" id="cost" name="cost" required>
            </div>

            <div class="mb-3">
                <label for="costPerLevel" class="form-label">Costo por Nivel</label>
                <input type="number" class="form-control" id="costPerLevel" name="costPerLevel" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descripción del Curso</label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label for="videoUpload" class="form-label">Subir Videos del Curso</label>
                <input class="form-control" type="file" id="videoUpload" name="videoUpload[]" multiple accept="video/*" required>
            </div>

            <button type="submit" class="btn btn-green">Registrar Curso</button>
        </form>
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
