<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel = "stylesheet" href="../styles/index.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light" style="margin-bottom: 50px;">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index/index.php" style="height: 40px; 
                align-items: center; display: flexbox; justify-content: center; text-align: center;">
                <img src="../Imagenes/APRENDIV2.png" alt="Logo" style="height: 20px; 
                align-items: center; display: flexbox; justify-content: center; text-align: center;"> Cursos Online
            </a>
            <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h3 class="text-center">Iniciar Sesión</h3>
                <form>
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" >
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" >
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Ingresar como:</label>
                        <select class="form-select" id="role" required>
                            <option value="instructor">Instructor</option>
                            <option value="estudiante">Estudiante</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-login">Ingresar</button>
                </form>
                <div id="error-message" class="error-message"></div>
                <p class="mt-3">¿No tienes una cuenta? <a href="../registro/registro.php">Regístrate aquí</a></p>
            </div>
        </div>
    </div>
    <!-- <script src="../scriptJS/login-val.js"></script> -->
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
