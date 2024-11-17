document.addEventListener('DOMContentLoaded', function () {
    // Cargar la lista de usuarios si el rol es administrador
    cargarUsuarios();

    // Filtrar usuarios al escribir en la barra de búsqueda
    document.getElementById('searchUser').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        filtrarUsuarios(query);
    });
});

function cargarUsuarios() {
    fetch('http://localhost/aprendi/api/usuariosController.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar los usuarios');
            }
            return response.json();
        })
        .then(data => {
            const usuariosContainer = document.getElementById('usuariosContainer');
            usuariosContainer.innerHTML = '';
            data.forEach(usuario => {
                // Filtrar para que solo se muestren usuarios que no sean administradores
                if (usuario.rol !== 'administrador') {
                    const usuarioElement = document.createElement('div');
                    usuarioElement.classList.add('usuario', 'card', 'mb-3', 'p-3', 'shadow-sm');
                    usuarioElement.innerHTML = `
                        <div class="d-flex align-items-center mb-2">
                            <img src="${usuario.foto || '../Imagenes/img-default.png'}" alt="Imagen del usuario" class="usuario-imagen rounded-circle me-3" style="width: 50px; height: 50px;">
                            <div>
                                <p class="mb-0 fw-bold usuario-nombre">${usuario.nombre}</p>
                                <small class="text-muted usuario-correo">${usuario.correo}</small>
                            </div>
                        </div>
                        <button class="btn ${usuario.estado == 0 ? 'btn-success' : 'btn-danger'} btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" onclick="prepararAccionUsuario(${usuario.id}, '${usuario.estado == 0 ? 'desbloquear' : 'bloquear'}')">
                            ${usuario.estado == 0 ? 'Desbloquear' : 'Bloquear'}
                        </button>
                    `;
                    usuariosContainer.appendChild(usuarioElement);
                }
            });
        })
        .catch(error => {
            console.error('Error al cargar los usuarios:', error);
        });
}

// Función para filtrar usuarios por nombre o correo
function filtrarUsuarios(query) {
    const usuarios = document.querySelectorAll('.usuario');
    usuarios.forEach(usuario => {
        const nombre = usuario.querySelector('.usuario-nombre').textContent.toLowerCase();
        const correo = usuario.querySelector('.usuario-correo').textContent.toLowerCase();
        if (nombre.includes(query) || correo.includes(query)) {
            usuario.style.display = 'block';
        } else {
            usuario.style.display = 'none';
        }
    });
}

// Función para bloquear a un usuario
function bloquearUsuario(usuarioId) {
    console.log('id usuarios:',usuarioId);
    fetch(`http://localhost/aprendi/api/usuariosController.php?id=${usuarioId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ estado: 0 })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al bloquear el usuario');
        }
        return response.json();
    })
    .then(data => {
        document.getElementById('searchUser').value = ''; // Limpiar la barra de búsqueda
        cargarUsuarios();
    })
    .catch(error => {
        console.error('Error al bloquear el usuario:', error);
    });
}

function prepararAccionUsuario(usuarioId, accion) {
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    confirmDeleteButton.setAttribute('onclick', `bloquearDesbloquearUsuario(${usuarioId}, '${accion}')`);
    document.getElementById('confirmDeleteModalLabel').innerText = `Confirmar ${accion}`;
    document.getElementById('confirmDeleteButton').innerText = `${accion.charAt(0).toUpperCase() + accion.slice(1)}`;
}

// Modal para confirmar acción de usuario
document.body.insertAdjacentHTML('beforeend', `
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar acción</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            ¿Está seguro de que desea realizar esta acción?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="confirmDeleteButton">Acción</button>
          </div>
        </div>
      </div>
    </div>
    `);

function bloquearDesbloquearUsuario(usuarioId, accion) {
    fetch(`http://localhost/aprendi/api/usuariosController.php?id=${usuarioId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ estado: accion === 'bloquear' ? 0 : 1 })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error al ${accion} el usuario`);
        }
        return response.json();
    })
    .then(data => {
        document.getElementById('searchUser').value = ''; // Limpiar la barra de búsqueda
        cargarUsuarios();
    })
    .catch(error => {
        console.error(`Error al ${accion} el usuario:`, error);
    });
}
