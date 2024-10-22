<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index/index.php">
                <img src="../Imagenes/APRENDIV2.png" alt="Logo" style="height: 20px;"> Cursos Online
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="container mt-5">
        <h3 class="text-center">Iniciar Sesión</h3>
        <div class="form-container mt-4">
            <form id="loginForm">
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo</label>
                    <input type="email" class="form-control" id="correo" name="correo">
                    <span id="error-correo" class="error-message"></span> 
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <span id="error-password" class="error-message"></span> 
                </div>
                <button type="submit" class="btn btn-custom">Ingresar</button>
                <div id="error-message" style="color: red;"></div> <!-- Mensaje de error general -->
                <br>
                <a class="mb-3" href="../registro/registro.php">¿No estas registrado?</a>
            </form>
        </div>
    </div>

    <!-- Modal de éxito -->
    <div class="modal fade" id="modalExito" tabindex="-1" aria-labelledby="modalExitoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExitoLabel">Inicio de Sesión Exitoso</h5>
                </div>
                <div class="modal-body">
                    Has iniciado sesión correctamente.
                </div>
                <div class="modal-footer">
                    <button type="button" id="cerrarModal" class="btn btn-primary">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../scriptJS/login-val.js"></script>
    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
        <!-- Incluir el menú y el footer con JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../partials/footer.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('footer-container').innerHTML = data;
                });
        });
        </script>
</body>
</html>
