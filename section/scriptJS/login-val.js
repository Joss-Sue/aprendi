document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault(); 

    limpiarMensajesError();

    const correo = document.getElementById('correo').value.trim();
    const password = document.getElementById('password').value.trim();

    let valid = true;

    if (correo === '') {
        mostrarMensajeError('error-correo', 'Por favor, ingresa un correo válido.');
        valid = false;
    }
    if (password === '') {
        mostrarMensajeError('error-password', 'Por favor, ingresa una contraseña válida.');
        valid = false;
    }

    if (!valid) {
        return;
    }

    const data = {
        correo: correo,
        contrasena: password
    };
    
    fetch('http://localhost/aprendi/api/usuariosController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json', 
            'ACTION': 'Login' 
        },
        body: JSON.stringify(data) 
    })
    .then(response => {
        return response.text();  
    })
    .then(data => {
        try {
            const jsonData = JSON.parse(data); 
            if (jsonData.status === 'success') {
                mostrarModalExito(); 
            } else {
                mostrarMensajeError('error-message', jsonData.message || 'Usuario o contraseña incorrectos.');
            }
        } catch (error) {
            // Si hay un error mostrar un mensaje
            mostrarMensajeError('error-message', 'La respuesta del servidor no es válida.');
        }
    })
    .catch(error => {
        mostrarMensajeError('error-message', 'Hubo un problema con el servidor.');
    });
});

// mostrar los mensajes de error
function mostrarMensajeError(elementId, mensaje) {
    const errorElement = document.getElementById(elementId);
    errorElement.innerText = mensaje;
    errorElement.style.color = "red";
}

// limpiar todos los mensajes de error antes de una nueva validación
function limpiarMensajesError() {
    document.getElementById('error-correo').innerText = '';
    document.getElementById('error-password').innerText = '';
    document.getElementById('error-message').innerText = '';
}

// mostrar el modal de éxito y redirigir a la página principal
function mostrarModalExito() {
    var modal = new bootstrap.Modal(document.getElementById('modalExito'));
    modal.show();

    // Redirigir 
    document.getElementById('cerrarModal').addEventListener('click', function() {
        window.location.href = '../index/index.php';  // Redirigir al index
    });
}
