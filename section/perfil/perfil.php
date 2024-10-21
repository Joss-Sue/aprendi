<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css"> <!-- Reutilizando estilos existentes -->
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <button class="return" onclick="history.back()">Regresar</button>
    <div class="container mt-5">
        <h3 class="text-center">Mi Perfil</h3>
        <form id="perfilForm" class="mt-4">
            <div id="error-message" class="error-message"></div>
            <div class="mb-3 text-center">
                <img src="ruta/a/tu/avatar.png" alt="Avatar" class="img-thumbnail" style="width: 150px;">
                <input type="file" id="avatar" class="form-control mt-2">
            </div>
            <div class="mb-3">
                <label for="fullname" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="fullname" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Género</label>
                <select class="form-select" id="gender" required>
                    <option value="male">Masculino</option>
                    <option value="female">Femenino</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="birthdate" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="birthdate" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" required>
                <small class="form-text text-muted">Debe tener al menos 8 caracteres, incluir una mayúscula, un número y un carácter especial.</small>
            </div>
            <button type="submit" class="btn btn-green">Guardar Cambios</button>
        </form>
        
        <script src="../perfil/perfil-val.js"></script>
        
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
