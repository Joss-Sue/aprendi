<?php
// Incluir verificación de sesión, pero sin forzar la existencia de la sesión
//include("../../config/sessionVerif.php");

// Verificar si la sesión está activa
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    // Hacer la solicitud para obtener los datos del usuario
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://localhost/aprendi/api/usuariosController.php?id=" . $usuario_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);

    $usuarioDatos = json_decode($response, true);

    if (isset($usuarioDatos['status']) && $usuarioDatos['status'] === 'error') {
        echo "Error al obtener los datos del usuario: " . $usuarioDatos['message'];
    } else {
        $correo = $usuarioDatos['correo'];
        $rol = $usuarioDatos['rol'];
    }
} else {
    // Si no hay sesión, establecer valores predeterminados
    $rol = null;  // Sin rol específico
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

    <!-- Contenedor principal -->
<div class="container-fluid mt-4">
    <div class="row">
            <!-- Lista de categorías en la columna izquierda -->
        <div class="col-md-3">
            <div class="category-list" style="width: 300px;">
                    <h5>Categorías</h5>
                    <ul class="list-unstyled" id="category-list">
                        <!-- Las categorías -->
                    </ul>
                <div class="input-group">
                        <button class="btn btn-outline-secondary" id="advancedSearchToggle">
                            <i class="bi bi-arrow-down"></i>
                        </button>
                    <!-- Buscador avanzado -->
                    <form id="searchForm">
                        <div class="advanced-search mb-4" id="advancedSearch" style="height: 140px;">
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
                        </div>
                    </form>
                </div>
                <button id="clearFilters" class="btn btn-green">Limpiar</button>
            </div>
        </div>
            <!-- barra de búsqueda, cursos -->
        <div class="col-md-9">
            <form id="searchFormsearch">
                <div class="search-bar mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchTitle" placeholder="Buscar por nombre de curso...">
                    </div>
                </div>
            </form>
                <!-- Contenedor de cursos -->
                <div id="courses-container" class="row"></div>
        </div>
    </div>
</div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
    <script src="../scriptJS/index-val.js"></script>
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
        });
    </script>

</body>
</html>