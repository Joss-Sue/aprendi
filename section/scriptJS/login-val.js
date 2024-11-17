let intentosFallidos = 0;
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
                intentosFallidos = 0; // Reiniciar el contador en caso de éxito
            } else {
                intentosFallidos++;
                mostrarMensajeError('error-message', jsonData.message || 'Usuario o contraseña incorrectos.');
                if (intentosFallidos >= 3) {
                    obtenerUsuarioPorCorreo(correo);
                }
            }
        } catch (error) {
            // Si hay un error mostrar un mensaje
            mostrarMensajeError('error-message', 'Usuario o contraseña incorrectos.');
        }
    })
    .catch(error => {
        mostrarMensajeError('error-message', 'Hubo un problema con el servidor.');
    });
});

// Obtener usuario por correo para eliminarlo
function obtenerUsuarioPorCorreo(correo) {
    fetch(`http://localhost/aprendi/api/usuariosController.php?correo=${correo}`)
        .then(response => response.json())
        .then(data => {
            if (data && data.id) {
                // Llamar a la función para deshabilitar al usuario
                bloquearUsuario(data.id);
            } else {
                mostrarMensajeError('error-message', 'No se pudo encontrar el usuario.');
            }
        })
        .catch(error => {
            mostrarMensajeError('error-message', 'Hubo un problema al obtener los datos del usuario.');
        });
}

function bloquearUsuario(usuarioId) {
    const data = { id: usuarioId };

    fetch('http://localhost/aprendi/api/usuariosController.php', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                mostrarMensajeError('error-message', 'Tu cuenta ha sido deshabilitada debido a múltiples intentos fallidos.');
            } else {
                mostrarMensajeError('error-message', 'Error al deshabilitar la cuenta: ' + data.message);
            }
        })
        .catch(error => {
            mostrarMensajeError('error-message', 'Hubo un problema al deshabilitar la cuenta.');
        });
}
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
