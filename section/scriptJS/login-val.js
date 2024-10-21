document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Limpiar mensajes de error previos
    document.getElementById('error-message').innerText = '';

    let username = document.getElementById('username').value.trim();
    let password = document.getElementById('password').value.trim();
    let errorMessage = '';
    let valid = true;

    // Validar campos vacíos
    if (!username || !password) {
        errorMessage += 'Por favor, completa todos los campos.<br>';
        valid = false;
    }

    // Validar que el usuario no sea solo números o símbolos
    const usernamePattern = /^[A-Za-z]+[A-Za-z0-9]*$/;
    if (!usernamePattern.test(username)) {
        errorMessage += 'El nombre de usuario debe contener al menos una letra y no puede ser solo números o símbolos.<br>';
        valid = false;
    }

    // Validar longitud mínima de la contraseña
    if (password.length < 8) {
        errorMessage += 'La contraseña debe tener al menos 8 caracteres.<br>';
        valid = false;
    }

    // Mostrar mensaje de error si no es válido
    if (!valid) {
        document.getElementById('error-message').innerHTML = errorMessage;
    } else {
        // Redirigir a la página principal si todo es correcto
        alert('Inicio de sesión exitoso');
        window.location.href = 'index.html';
    }
});
