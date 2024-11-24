document.addEventListener('DOMContentLoaded', function () {
    cargarCategorias();
});

function cargarCategorias() {
    fetch(`http://localhost/aprendi/api/categoriaController.php?`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("Error al obtener las categorias");
        }
        return response.json();
    })
    .then(data => {
            const listaCategorias = document.getElementById('listaCategorias');
            listaCategorias.innerHTML = '';
            data.forEach(categoria => {
                const categoriaElement = document.createElement('li');
                categoriaElement.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                categoriaElement.innerHTML = `
                    ${categoria.nombre}
                    <div>
                        <button class="btn btn-warning btn-sm me-2" onclick="editarCategoria(${categoria.id}, '${categoria.nombre}', '${categoria.descripcion}')">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="prepararBorrarCategoria(${categoria.id})" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Borrar</button>
                    </div>
                `;
                listaCategorias.appendChild(categoriaElement);
            });
        })
        .catch(error => {
            console.error('Error al cargar las categorías:', error);
        });
}

function editarCategoria(id, nombre, descripcion) {
    document.getElementById('categoriaId').value = id;
    document.getElementById('nombreCategoria').value = nombre;
    document.getElementById('descripcionCategoria').value = descripcion;
    document.getElementById('formularioTitulo').innerText = 'Editar Categoría';
    document.getElementById('btnEditar').style.display = 'inline-block';
    document.getElementById('btnGuardar').style.display = 'none';
}

function limpiarFormulario() {
    document.getElementById('categoriaId').value = '';
    document.getElementById('nombreCategoria').value = '';
    document.getElementById('descripcionCategoria').value = '';
    document.getElementById('formularioTitulo').innerText = 'Registrar Categoría';
    document.getElementById('btnEditar').style.display = 'none';
    document.getElementById('btnGuardar').style.display = 'inline-block';
}

document.getElementById('btnGuardar').addEventListener('click', function () {
    guardarCategoria();
});

document.getElementById('btnEditar').addEventListener('click', function () {
    actualizarCategoria();
});

function guardarCategoria() {
    const nombre = document.getElementById('nombreCategoria').value;
    const descripcion = document.getElementById('descripcionCategoria').value;
    const createdBy = usuarioId;

    if (nombre && descripcion) {
        const url = 'http://localhost/aprendi/api/categoriaController.php';
        const datos = { nombre, descripcion, createdBy };

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al guardar la categoría');
            }
            return response.text();
        })
        .then(data => {
            // Mostrar el modal de éxito solo si no hay errores
            mostrarModalExito('Categoría guardada con éxito');
            cargarCategorias();
            limpiarFormulario();
        })
        .catch(error => {
            mostrarModalError('Error al guardar la categoría');
            console.error('Error al guardar la categoría:', error);
        });
    } else {
        mostrarModalError('Por favor complete todos los campos.');
    }
}

function actualizarCategoria() {
    const id = document.getElementById('categoriaId').value;
    const nombre = document.getElementById('nombreCategoria').value;
    const descripcion = document.getElementById('descripcionCategoria').value;
    const createdBy = usuarioId;

    if (nombre && descripcion) {
        const url = 'http://localhost/aprendi/api/categoriaController.php';
        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({id, nombre, descripcion, createdBy })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al actualizar la categoría');
            }
            return response.json();
        })
        .then(data => {
            mostrarModalExito('Categoría actualizada con éxito');
            cargarCategorias();
            limpiarFormulario();
        })
        .catch(error => {
            mostrarModalError('Error al actualizar la categoría');
            console.error('Error al actualizar la categoría:', error);
        });
    } else {
        mostrarModalError('Por favor complete todos los campos.');
    }
}

function prepararBorrarCategoria(id) {
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    confirmDeleteButton.setAttribute('onclick', `borrarCategoria(${id})`);
}

function borrarCategoria(id) {
    const url = 'http://localhost/aprendi/api/categoriaController.php';
    fetch(url, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al eliminar la categoría');
        }
        return response.json();
    })
    .then(data => {
        mostrarModalExito('Categoría eliminada con éxito');
        cargarCategorias();
        const confirmDeleteModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
        confirmDeleteModal.hide();
    })
    .catch(error => {
        mostrarModalError('Error al eliminar la categoría');
        console.error('Error al eliminar la categoría:', error);
    });
}

function mostrarModalExito(mensaje) {
    const modalExito = new bootstrap.Modal(document.getElementById('successModal'));
    document.getElementById('successModalMensaje').innerText = mensaje;
    modalExito.show();
}

function mostrarModalError(mensaje) {
    const modalError = new bootstrap.Modal(document.getElementById('errorModal'));
    document.getElementById('errorModalMensaje').innerText = mensaje;
    modalError.show();
}

// Modales de éxito y error
document.body.insertAdjacentHTML('beforeend', `
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Éxito</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="successModalMensaje"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="errorModalMensaje"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Está seguro de que desea eliminar esta categoría?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
`);
