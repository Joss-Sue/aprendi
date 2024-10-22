<?php
include("../../config/sessionVerif.php");
?>
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
                <img src="../Imagenes/img-default.png" alt="Avatar" class="img-thumbnail" style="width: 150px;">
                <input type="file" id="avatar" class="form-control mt-2" disabled>
            </div>
            <!-- Campo para el nombre completo -->
            <div class="mb-3">
                <label for="fullname" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="fullname" name="fullname">
                <div id="error-fullname" class="error-message"></div>
            </div>
            <!-- Género (No modificable) -->
            <div class="mb-3">
                <label for="genero" class="form-label">Género</label>
                <select id="genero" name="genero" class="form-control" disabled>
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <!-- Fecha de Nacimiento (No modificable) -->
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" disabled>
            </div>
                <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email">
                <div id="error-email" class="error-message"></div>
            </div>
            <!-- Contraseña -->
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password">
                <div id="error-password" class="error-message"></div>
            </div>
            <button type="submit" class="btn btn-green">Guardar Cambios</button>
            <div id="error-message" class="error-message"></div>
        </form>
        <button type="submit" id="deleteAccountBtn" class="btn btn-custom">Eliminar Cuenta</button>     
    </div>

<!-- Modal para éxito -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="successModalLabel">Éxito</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            Actualización realizada con éxito.
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="cerrarModalExito">Cerrar</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Modal para confirmación de eliminación -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="deleteConfirmModalLabel">Confirmar eliminación de cuenta</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            ¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteAccount">Eliminar Cuenta</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Modal para éxito en eliminación de cuenta -->
    <div class="modal fade" id="deleteSuccessModal" tabindex="-1" aria-labelledby="deleteSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="deleteSuccessModalLabel">Cuenta eliminada</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            Tu cuenta ha sido eliminada exitosamente. La sesión ha sido cerrada.
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="cerrarModalEliminarExito">Cerrar</button>
        </div>
        </div>
    </div>
    </div>




    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
<script>
    const usuarioId = "<?php echo $_SESSION['usuario_id']; ?>";
</script>


    <script src="../scriptJS/perfil-val.js"></script>
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
