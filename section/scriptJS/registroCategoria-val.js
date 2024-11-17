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
    document.getElementById('btnGuardar').innerText = 'Guardar Cambios';
    const modal = new bootstrap.Modal(document.getElementById('categoriaModal'));
    modal.show();
}

function guardarCategoria() {
    const id = document.getElementById('categoriaId').value;
    const nombre = document.getElementById('nombreCategoria').value;
    const descripcion = document.getElementById('descripcionCategoria').value;
    if (nombre && descripcion) {
        const metodo = id ? 'POST' : 'PUT';
        const url = id ? `http://localhost/aprendi/api/categoriasController.php?id=${id}` : 'http://localhost/aprendi/api/categoriasController.php';

        fetch(url, {
            method: metodo,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ nombre, descripcion })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al guardar la categoría');
            }
            return response.json();
        })
        .then(data => {
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

function prepararBorrarCategoria(id) {
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    confirmDeleteButton.setAttribute('onclick', `borrarCategoria(${id})`);
}

function borrarCategoria(id) {
    fetch(`http://localhost/aprendi/api/categoriasController.php?id=${id}`, {
        method: 'DELETE'
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
    })
    .catch(error => {
        mostrarModalError('Error al eliminar la categoría');
        console.error('Error al eliminar la categoría:', error);
    });
}

function limpiarFormulario() {
    document.getElementById('categoriaId').value = '';
    document.getElementById('nombreCategoria').value = '';
    document.getElementById('descripcionCategoria').value = '';
    document.getElementById('formularioTitulo').innerText = 'Registrar Categoría';
    document.getElementById('btnGuardar').innerText = 'Agregar Categoría';
}

document.getElementById('btnGuardar').addEventListener('click', function () {
    guardarCategoria();
});

document.getElementById('btnCancelar').addEventListener('click', function () {
    limpiarFormulario();
});

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