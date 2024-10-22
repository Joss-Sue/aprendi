<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.html">
                <img src="Imagenes/APRENDIV2.png" alt="Logo" style="height: 20px;"> Cursos Online
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
        <select class="form-select" id="role" name="role" required>
            <option value="instructor">Instructor</option>
            <option value="estudiante">Estudiante</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="fullname" class="form-label">Nombre Completo</label>
        <input type="text" class="form-control" id="fullname" name="fullname" required>
    </div>
    <div class="mb-3">
        <label for="gender" class="form-label">Género</label>
        <select class="form-select" id="gender" name="gender" required>
            <option value="male">Masculino</option>
            <option value="female">Femenino</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="birthdate" class="form-label">Fecha de Nacimiento</label>
        <input type="date" class="form-control" id="birthdate" name="birthdate" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required>
        <small class="form-text text-muted">Debe tener al menos 8 caracteres, incluir una mayúscula, un número y un carácter especial.</small>
    </div>
    <div class="mb-3">
        <label for="avatar" class="form-label">Foto de Perfil</label>
        <input type="file" class="form-control" id="avatar" name="avatar">
    </div>
    <button type="submit" class="btn btn-custom">Registrar</button>
</form>

            <div id="error-message" class="error-message"></div>
        </div>
    </div>
   
    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
    <!-- Incluir el menú y el footer con JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('registroForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita que el formulario se envíe de la manera convencional

    // Recolectar los datos del formulario ejemplo
    const formData = new FormData(this);
    const data = {
        rol: formData.get('role'),
        nombre: formData.get('fullname'),
        genero: formData.get('gender'),
        fecha_nac: formData.get('birthdate'),
        correo: formData.get('email'),
        contrasena: formData.get('password')
    };

    // Si necesitas enviar la foto de perfil (avatar), puedes manejarlo de otra manera.
    //if (formData.get('avatar')) {
        // Aquí puedes procesar la imagen si es necesario, por ejemplo, subirla a un servidor.
    //}

    // Enviar los datos a la API
    fetch('http://localhost/aprendiv1/api/usuariosController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json', // Especifica que estás enviando JSON
        },
        body: JSON.stringify(data) // Convierte los datos a formato JSON
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la solicitud');
            console.log("entre al throw");
        }
        return response.json();
        alert("exitoso 1");

    })
    .then(data => {
        console.log('Éxito:', data);
        alert("exitoso 2");

        // Aquí puedes manejar la respuesta de la API
    })
    .catch((error) => {
        console.error('Error:', error);
        console.log(data);
    });
});

    </script>
</body>
</html>