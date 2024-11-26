document.addEventListener('DOMContentLoaded', function () {
    cargarCategorias();
    cargarCursos();

    // Eventos de filtros
    document.getElementById('category').addEventListener('change', cargarCursos);
    document.getElementById('activeCourses').addEventListener('change', cargarCursos);
});

function cargarCategorias() {
    const url = 'http://localhost/aprendi/api/categoriaController.php';
    fetch(url)
        .then((response) => {
            if (!response.ok) {
                throw new Error('Error al cargar las categorías');
            }
            return response.json();
        })
        .then((categorias) => {
            const selectCategoria = document.getElementById('category');
            selectCategoria.innerHTML = '<option value="0">Todas</option>'; // Opción para todas las categorías
            categorias.forEach((categoria) => {
                const option = document.createElement('option');
                option.value = categoria.id; // Cambia según el nombre del campo en tu API
                option.textContent = categoria.nombre;
                selectCategoria.appendChild(option);
            });
        })
        .catch((error) => console.error('Error al cargar categorías:', error));
}

function cargarCursos() {
    const idCategoria = document.getElementById('category').value || 0;
    const estado = document.getElementById('activeCourses').checked ? 1 : 0;

    const url = `http://localhost/aprendi/api/reportesController.php/?tipo=INSTRUCTOR&id=${usuarioId}&categoria=0&estado=0`;
    fetch(url)
        .then((response) => {
            if (!response.ok) {
                throw new Error('Error al cargar los cursos');
            }
            return response.json();
        })
        .then((cursos) => {
            console.log(cursos);
            const tablaCursos = document.querySelector('.table tbody');
            tablaCursos.innerHTML = ''; // Limpiar tabla

            let totalIngresos = 0;

            // Verifica si cursos es un arreglo o un objeto
            const cursosArray = Array.isArray(cursos) ? cursos : [cursos];

            if (cursosArray.length === 0) {
                tablaCursos.innerHTML = '<tr><td colspan="4">No hay cursos disponibles</td></tr>';
                return;
            }

            cursosArray.forEach((curso) => {
                totalIngresos += parseFloat(curso.promedio_precio_pagado || 0);

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${curso.curso_titulo}</td>
                    <td>${curso.total_estudiantes || 0}</td>
                    <td>${curso.promedio_progreso_curso || 0}%</td>
                    <td>$${curso.promedio_precio_pagado || 0}</td>
                `;
                row.addEventListener('click', () => cargarEstudiantes(curso.curso_id));
                tablaCursos.appendChild(row);
            });

            document.querySelector('.table tfoot td:last-child').textContent = `$${totalIngresos.toFixed(2)}`;
        })
        .catch((error) => console.error('Error al cargar los cursos:', error));
}


function cargarEstudiantes(cursoId) {
    const url = `http://localhost/aprendi/api/reportesController.php/?tipo=INSTRUCTOR&id_curso=${cursoId}`;
    console.log(`Cargando estudiantes para curso ID: ${cursoId}`);

    fetch(url)
        .then((response) => {
            if (!response.ok) {
                throw new Error('Error al cargar los estudiantes');
            }
            return response.json();
        })
        .then((estudiantes) => {
            console.log(estudiantes);
            const tablaAlumnos = document.querySelectorAll('.table tbody')[1];
            tablaAlumnos.innerHTML = ''; // Limpiar tabla

            let totalIngresos = 0;
            if (estudiantes.length === 0) {
                tablaAlumnos.innerHTML = '<tr><td colspan="5">No hay alumnos inscritos en este curso</td></tr>';
                return;
            }

            estudiantes.forEach((alumno) => {
                totalIngresos += parseFloat(alumno.precio_pagado || 0);

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${alumno.estudiante_nombre}</td>
                    <td>${alumno.fecha_inscripcion}</td>
                    <td>${alumno.nivel_avance || 0}%</td>
                    <td>$${alumno.precio_pagado || 0}</td>
                    <td>${alumno.forma_pago || 'N/A'}</td>
                `;
                tablaAlumnos.appendChild(row);
            });

            document.querySelectorAll('.table tfoot td:last-child')[1].textContent = `$${totalIngresos.toFixed(2)}`;
        })
        .catch((error) => console.error('Error al cargar los estudiantes:', error));
}
