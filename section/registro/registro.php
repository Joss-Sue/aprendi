<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
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
        <h3 class="text-center">Registro de Usuario</h3>
        <div class="form-container mt-4">
            <form id="registroForm">
                <div class="mb-3">
                    <label for="role" class="form-label">Registrarse como:</label>
                    <select class="form-select" id="role" name="role" >
                        <option value="instructor">Instructor</option>
                        <option value="estudiante">Estudiante</option>
                    </select>
                    <span class="error-message" id="error-role"></span>
                </div>
                <div class="mb-3">
                    <label for="fullname" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" >
                    <span class="error-message" id="error-fullname"></span>
                </div>
                <div class="mb-3">
                    <label for="gender" class="form-label">Género</label>
                    <select class="form-select" id="gender" name="gender" >
                        <option value="male">Masculino</option>
                        <option value="female">Femenino</option>
                    </select>
                    <span class="error-message" id="error-gender"></span>
                </div>
                <div class="mb-3">
                    <label for="birthdate" class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" id="birthdate" name="birthdate" >
                    <span class="error-message" id="error-birthdate"></span>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" >
                    <span class="error-message" id="error-email"></span>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" >
                    <small class="form-text text-muted">Debe tener al menos 8 caracteres, incluir una mayúscula, un número y un carácter especial.</small>
                    <span class="error-message" id="error-password"></span>
                </div>
                <div class="mb-3">
                    <label for="avatar" class="form-label">Foto de Perfil</label>
                    <input type="file" class="form-control" id="avatar" name="avatar">
                    <div id="error-img" style="color: red;"></div>
                </div>
                <button type="submit" class="btn btn-custom">Registrar</button>
                <div id="error-message" style="color: red;"></div>
                <br>
                <a class="mb-3" href="../login/login.php">Ya tengo cuenta</a>
            </form>

            <!-- Modal de éxito con Bootstrap -->
            <div class="modal fade" id="modalExito" tabindex="-1" aria-labelledby="modalExitoLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalExitoLabel">Registro Exitoso</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            El usuario ha sido registrado con éxito.
                        </div>
                        <div class="modal-footer">
                            <button id="cerrarModal" type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>

    <!-- Scripts -->
    <script src="../scriptJS/registro-val.js"></script>
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
