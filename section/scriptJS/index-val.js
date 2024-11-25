document.addEventListener('DOMContentLoaded', function () {
    cargarCategorias();
    cargarCursos();

    document.getElementById('advancedSearchToggle').addEventListener('click', function() {
        const advancedSearch = document.getElementById('advancedSearch');
        advancedSearch.style.display = (advancedSearch.style.display === 'none' || advancedSearch.style.display === '') ? 'block' : 'none';
    });

    document.getElementById('searchForm').addEventListener('input', aplicarFiltro);
    document.getElementById('searchFormsearch').addEventListener('input', aplicarFiltro);

    //limpiar filtros
    document.getElementById('clearFilters').addEventListener('click', limpiarFiltros);
});

function cargarCursos() {
    fetch('http://localhost/aprendi/api/cursoController.php?pagina=1')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar los cursos');
            }
            return response.json();
        })
        .then(data => {
            cursosCargados = data; // Guardar los cursos cargados para filtrarlos luego
            mostrarCursos(data);
        })
        .catch(error => {
            console.error('Error al cargar los cursos:', error);
        });
}

function mostrarCursos(cursos) {
    const container = document.getElementById('courses-container');
    container.innerHTML = '';
    cursos.forEach(curso => {
        const courseCard = document.createElement('div');
        courseCard.classList.add('col-md-4');
        
        // Obtener la URL de la imagen en formato base64, si está disponible
        const imagenCurso = curso.imagen ? `<img src="${curso.imagen}" class="card-img-top" alt="${curso.titulo}" style="height: 200px; object-fit: cover;">` : 
                                           `<div class="card-img-top" style="background-color: #ccc; height: 200px; display: flex; align-items: center; justify-content: center;"><span style="color: #555;">Imagen no disponible</span></div>`;

        courseCard.innerHTML = `
            <div class="card course-card">
                ${imagenCurso}
                <div class="card-body">
                    <h5 class="card-title">${curso.titulo}</h5>
                    <p class="card-text">${curso.descripcion}</p>
                    <p><strong>Costo:</strong> $${curso.costo}</p>
                    <a href="../cursos/curso.php?id=${curso.id}" class="btn btn-green">Ver Curso</a>
                </div>
            </div>
        `;
        container.appendChild(courseCard);
    });
}


function cargarCategorias() {
    fetch('http://localhost/aprendi/api/categoriaController.php')
        .then(response => response.json())
        .then(data => {
            const categoryMenu = document.getElementById('category-list');
            categoryMenu.innerHTML = '';

            data.forEach(categoria => {
                const li = document.createElement('li');
                li.innerHTML = `<a href="#" class="text-dark" id="category" onclick="filtrarPorCategoria('${categoria.id}')">${categoria.nombre}</a>`;
                categoryMenu.appendChild(li);
            });
        })
        .catch(error => console.error('Error al cargar las categorías:', error));
}

let cursosCargados = [];

// Función para aplicar los filtros
function aplicarFiltro() {
    const titulo = document.getElementById('searchTitle')?.value.toLowerCase() || '';
    const fechaInicio = document.getElementById('startDate')?.value || '';
    const fechaFin = document.getElementById('endDate')?.value || '';
    const categoriaId = document.getElementById('category')?.value || '';

    let cursosFiltrados = cursosCargados;

    // Filtrar por título
    if (titulo) {
        cursosFiltrados = cursosFiltrados.filter(curso => curso.titulo.toLowerCase().includes(titulo));
    }

    // Filtrar por categoría
    if (categoriaId) {
        cursosFiltrados = cursosFiltrados.filter(curso => curso.categoria_id == categoriaId);
    }

    // fechas de creacion
    if (fechaInicio) {
        const fechaInicioDate = new Date(fechaInicio);
        fechaInicioDate.setHours(0, 0, 0, 0); // Normalizar a medianoche

        cursosFiltrados = cursosFiltrados.filter(curso => {
            const fechaCurso = new Date(curso.fecha_creacion);
            fechaCurso.setHours(0, 0, 0, 0); // Normalizar a medianoche
            return fechaCurso.getTime() >= fechaInicioDate.getTime();
        });
    }

    if (fechaFin) {
        const fechaFinDate = new Date(fechaFin);
        fechaFinDate.setHours(24, 59, 59, 999);

        cursosFiltrados = cursosFiltrados.filter(curso => {
            const fechaCurso = new Date(curso.fecha_creacion);
            fechaCurso.setHours(0, 0, 0, 0);
            return fechaCurso.getTime() <= fechaFinDate.getTime();
        });
    }

    mostrarCursos(cursosFiltrados);
}

function filtrarPorCategoria(categoriaId) {
    document.getElementById('category').value = categoriaId;
    aplicarFiltro();
}
// Función para limpiar los filtros
function limpiarFiltros() {
    document.getElementById('searchTitle').value = '';
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('category').value = '';
    mostrarCursos(cursosCargados);
}
