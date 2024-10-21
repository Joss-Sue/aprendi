<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
    <style>
        /* Estilo personalizado para la lista */
        .category-list {
            text-align: center;
            margin-top: 30px;
        }
        .category-item {
            font-size: 1.5rem; /* Tamaño de fuente más grande */
            margin: 10px 0;
        }
        .add-category-form {
            margin: 20px auto;
            text-align: center;
        }
        .action-buttons {
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <div class="container">
        <h2 class="text-center mt-5">Registro de Categorías</h2>

        <!-- Formulario para agregar nueva categoría -->
        <div class="add-category-form">
            <form class="mb-4">
                <input type="text" class="form-control" placeholder="Nueva Categoría" required>
                <button type="submit" class="btn btn-primary mt-2">Agregar Categoría</button>
            </form>
        </div>

        <!-- Lista de categorías -->
        <div class="category-list">
            <h3>Categorías Registradas</h3>
            <ul class="list-unstyled">
                <li class="category-item">
                    IT & Software
                    <div class="action-buttons">
                        <button class="btn btn-warning btn-sm">Editar</button>
                        <button class="btn btn-danger btn-sm">Borrar</button>
                    </div>
                </li>
                <li class="category-item">
                    Marketing
                    <div class="action-buttons">
                        <button class="btn btn-warning btn-sm">Editar</button>
                        <button class="btn btn-danger btn-sm">Borrar</button>
                    </div>
                </li>
                <li class="category-item">
                    Design
                    <div class="action-buttons">
                        <button class="btn btn-warning btn-sm">Editar</button>
                        <button class="btn btn-danger btn-sm">Borrar</button>
                    </div>
                </li>
            </ul>
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
