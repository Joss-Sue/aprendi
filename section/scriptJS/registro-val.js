document.getElementById('registroForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevenir el envío del formulario para validación

    // Limpiar mensajes de error previos
    document.getElementById('error-message').innerText = '';

    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=<>?{}[\]~]).{8,}$/;
    let errorMessage = '';
    let valid = true;

    // Validar que los campos no estén vacíos
    const requiredFields = document.querySelectorAll('#registroForm [required]');
    requiredFields.forEach(field => {
        if (field.value.trim() === '') {
            errorMessage += `El campo ${field.previousElementSibling.innerText} es obligatorio.<br>`;
            valid = false;
        }
    });

    // Validar formato de email
    if (!emailPattern.test(email)) {
        errorMessage += 'Por favor, introduce un correo electrónico válido.<br>';
        valid = false;
    }

    // Validar que la contraseña cumpla con los requisitos
    if (!passwordPattern.test(password)) {
        errorMessage += 'La contraseña debe tener al menos 8 caracteres, incluir una mayúscula, un número y un carácter especial.<br>';
        valid = false;
    }

    // Mostrar mensaje de error si no es válido
    if (!valid) {
        document.getElementById('error-message').innerHTML = errorMessage;
    } else {
        this.submit(); // Enviar el formulario si todas las validaciones pasan
    }
});
