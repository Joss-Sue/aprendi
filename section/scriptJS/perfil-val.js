document.addEventListener('DOMContentLoaded', function() {
    // Verificar si el usuarioId está definido y obtener los datos
    if (usuarioId) {
        fetch(`http://localhost/aprendi/api/usuariosController.php?id=${usuarioId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Error al obtener los datos del usuario");
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' || data.id) {
                // Llenar los inputs con los datos obtenidos
                if (!data.foto) {
                    throw new Error("La imagen del usuario no está disponible.");
                }
                // Llenar los inputs con los datos obtenidos
                const avatarUrl = `${data.foto}`;

                document.getElementById('avatar').src = avatarUrl;
                document.getElementById('fullname').value = data.nombre;
                document.getElementById('email').value = data.correo;
                document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento;

                if (data.genero === 'male') {
                    document.getElementById('genero').value = 'Masculino';
                } else if (data.genero === 'female') {
                    document.getElementById('genero').value = 'Femenino';
                } else {
                    document.getElementById('genero').value = 'Otro';
                }

                // Mostrar la contraseña en el input
                document.getElementById('password').value = data.contrasena;
                // Mostrar el rol
                document.getElementById('rolusuario').innerText = `${data.rol}`;
            } else {
                console.error("Error al obtener los datos del usuario:", data.message);
            }
        })
        .catch(error => console.error('Error al obtener los datos del usuario:', error));
    } else {
        console.error('Usuario no está en sesión');
    }

    // Agregar el evento submit para validar y enviar los datos
    document.getElementById('perfilForm').addEventListener('submit', function(event) {
        event.preventDefault(); 

        limpiarMensajesError();

        const fullname = document.getElementById('fullname').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim(); 
        const imagenInput = document.getElementById('avatarInput');
        const avatarImg = document.getElementById('avatar');
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
        const namePattern = /^[a-zA-Z\s]{3,}$/; // Solo letras y al menos 3 caracteres
        let imagenBase64 = null;
        let valid = true;

        // Validación del nombre
        if (!namePattern.test(fullname)) {
            mostrarMensajeError('error-fullname', 'El nombre debe tener al menos 3 caracteres y solo puede contener letras.');
            valid = false;
        }

        // Validación del correo electrónico
        if (!emailPattern.test(email)) {
            mostrarMensajeError('error-email', 'Por favor, introduce un correo electrónico válido.');
            valid = false;
        }

        // Validación de la contraseña
        if (password === '') {
            mostrarMensajeError('error-password', 'La contraseña no puede estar vacía.');
            valid = false;
        } else if (!passwordPattern.test(password)) {
            mostrarMensajeError('error-password', 'La contraseña debe tener al menos 8 caracteres, incluir una mayúscula, un número y un carácter especial.');
            valid = false;
        }
        if (imagenInput.files.length > 0) {
            // Caso: Se seleccionó una nueva imagen
            convertirImagenABase64(imagenInput.files[0], function (base64) {
                if (base64) {
                    imagenBase64 = base64;
                    enviarFormulario(fullname, email, password, imagenBase64);
                } else {
                    mostrarMensajeError('error-img', 'Error al procesar la nueva imagen.');
                }
            });
        } else {
            // Caso: No se seleccionó una nueva imagen, usar la imagen actual
            const imagenActualSrc = avatarImg.src;
            if (!imagenActualSrc || imagenActualSrc.trim() === '') {
                mostrarMensajeError('error-img', 'Necesitas agregar una imagen.');
            } else {
                // Usar la imagen actual (asumimos que el `src` es válido, ya sea URL o base64)
                enviarFormulario(fullname, email, password, imagenActualSrc);
            }
        }
    });

// Función para convertir archivo en base64
function convertirImagenABase64(file, callback) {
    const reader = new FileReader();
    reader.onloadend = function () {
        callback(reader.result);
    };
    reader.onerror = function () {
        console.error("Error al leer el archivo.");
        callback(null);
    };
    reader.readAsDataURL(file);
}
    

    // Función para enviar los datos del perfil a la API
    function enviarFormulario(fullname, email, password, imagenBase64) {
        const data = {
            id: usuarioId,
            nombre: fullname,
            correo: email,
            contrasena: password !== '' ? password : undefined, // Enviar la contraseña si no está vacía
            imagen: imagenBase64 || null
        };
    console.log(data);
        fetch('http://localhost/aprendi/api/usuariosController.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.text()) // Obtener la respuesta como texto primero
        .then(responseText => {
            console.log('Respuesta del servidor:', responseText); // Imprimir la respuesta para ver qué está sucediendo
            try {
                const data = JSON.parse(responseText); // Intentar convertir el texto en JSON
                if (data.status === 'success') {
                    mostrarModalExito();
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error al parsear la respuesta:', error.message);
                throw new Error('La respuesta no es JSON válido: ' + responseText);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('error-message').innerText = "Error al actualizar. Por favor, inténtalo de nuevo.";
        });
    }
    

// Mostrar el modal de confirmación antes de eliminar la cuenta
document.getElementById('deleteAccountBtn').addEventListener('click', function(event) {
    event.preventDefault();

    const deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteConfirmModal.show();
});

// Cuando el usuario confirma la eliminación
document.getElementById('confirmDeleteAccount').addEventListener('click', function() {
    eliminarCuenta(); // Llamar a la función para eliminar la cuenta
});

// Función para eliminar la cuenta
function eliminarCuenta() {
    fetch(`http://localhost/aprendi/api/usuariosController.php?id=${usuarioId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: usuarioId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al eliminar la cuenta');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            // Mostrar el modal de éxito en la eliminación
            const deleteSuccessModal = new bootstrap.Modal(document.getElementById('deleteSuccessModal'));
            deleteSuccessModal.show();

            // Cerrar la sesión después de eliminar la cuenta
            fetch('http://localhost/aprendi/config/cerrarSesion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(() => {
                document.getElementById('cerrarModalEliminarExito').addEventListener('click', function() {
                    window.location.href = '../login/login.php'; // Redirigir al login
                });
            })
            .catch(error => {
                mostrarMensajeErrorElim('Error al cerrar la sesión. Por favor, inténtalo de nuevo.');
            });
        } else {
            mostrarMensajeErrorElim("Error al eliminar la cuenta: " + data.message);
        }
    })
    .catch(error => {
        mostrarMensajeErrorElim("Error al eliminar la cuenta. Por favor, inténtelo de nuevo.");
    });
}

    function mostrarMensajeErrorElim(mensaje) {
        const modalErrorMessage = document.getElementById('modalErrorMessage');
        modalErrorMessage.innerText = mensaje;

        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    }
    function mostrarMensajeError(elementId, mensaje) {
        const errorElement = document.getElementById(elementId);
        errorElement.innerText = mensaje;
        errorElement.style.color = "red";
    }

    function limpiarMensajesError() {
        document.getElementById('error-fullname').innerText = '';
        document.getElementById('error-email').innerText = '';
        document.getElementById('error-password').innerText = '';
    }

    function mostrarModalExito() {
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    }
    
});
