document.getElementById('registroForm').addEventListener('submit', function(event) {
    event.preventDefault(); 


    limpiarMensajesError();

    const role = document.getElementById('role').value.trim();
    const fullname = document.getElementById('fullname').value.trim();
    const gender = document.getElementById('gender').value.trim();
    const birthdate = document.getElementById('birthdate').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();

    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    let valid = true;

    // Validar que los campos no estén vacíos
    if (role === '') {
        mostrarMensajeError('error-role', 'El campo rol es obligatorio.');
        valid = false;
    }
    if (fullname === '') {
        mostrarMensajeError('error-fullname', 'El nombre completo es obligatorio.');
        valid = false;
    }
    if (gender === '') {
        mostrarMensajeError('error-gender', 'El género es obligatorio.');
        valid = false;
    }
    if (birthdate === '') {
        mostrarMensajeError('error-birthdate', 'La fecha de nacimiento es obligatoria.');
        valid = false;
    }
    if (!emailPattern.test(email)) {
        mostrarMensajeError('error-email', 'Por favor, introduce un correo electrónico válido.');
        valid = false;
    }
    if (!passwordPattern.test(password)) {
        mostrarMensajeError('error-password', 'La contraseña debe tener al menos 8 caracteres, incluir una mayúscula, un número y un carácter especial.');
        valid = false;
    }

    // Si hay algún error, no continuar
    if (!valid) {
        return;
    }

    // Si todo es válido, enviar el formulario a la API
    enviarFormulario();
});

// mostrar los mensajes de error
function mostrarMensajeError(elementId, mensaje) {
    const errorElement = document.getElementById(elementId);
    errorElement.innerText = mensaje;
    errorElement.style.color = "red";
}

// limpiar todos los mensajes de error antes de una nueva validación
function limpiarMensajesError() {
    document.getElementById('error-role').innerText = '';
    document.getElementById('error-fullname').innerText = '';
    document.getElementById('error-gender').innerText = '';
    document.getElementById('error-birthdate').innerText = '';
    document.getElementById('error-email').innerText = '';
    document.getElementById('error-password').innerText = '';
}

// Función para enviar los datos a la API
function enviarFormulario() {
    const formData = new FormData(document.getElementById('registroForm'));
    const data = {
        rol: formData.get('role'),
        nombre: formData.get('fullname'),
        genero: formData.get('gender'),
        fecha_nac: formData.get('birthdate'),
        correo: formData.get('email'),
        contrasena: formData.get('password')
    };
    

    fetch('http://localhost/aprendi/api/usuariosController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la solicitud');
        }
        return response.json();
    })
    .then(data => {
        mostrarModalExito(); // Mostrar el modal
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('error-message').innerText = "Error al registrar. Por favor, inténtalo de nuevo.";
    });
}

function mostrarModalExito() {
    var modal = new bootstrap.Modal(document.getElementById('modalExito'));
    modal.show();

    document.getElementById('cerrarModal').addEventListener('click', function() {
        document.getElementById('registroForm').reset();

        window.location.href = '../login/login.php'; // Redirige a la página de login
    });
}
