document.addEventListener('DOMContentLoaded', function () {
    
    const ventasCursosURL = `http://localhost/aprendi/api/reportesController.php?tipo=INSTRUCTOR&id=${usuarioId}&categoria=0&estado=1`;
    const ventasPorCursoBaseURL = 'http://localhost/aprendi/api/reportesController.php?tipo=INSTRUCTOR&curso_titulo=';

    // Contenedores de las tablas
    const tablaCursos = document.querySelector('table tbody:nth-of-type(1)');
    const tablaAlumnos = document.querySelector('table tbody:nth-of-type(2)');
    console.log(ventasCursosURL);

    // Obtener datos de los cursos del instructor
    fetch(ventasCursosURL)
        .then(response => response.json())
        .then(cursos => {
            if (Array.isArray(cursos)) {
                tablaCursos.innerHTML = ''; // Limpia la tabla antes de llenarla
                let totalIngresosCursos = 0;

                cursos.forEach(curso => {
                    // Crear fila para la tabla de cursos
                    const row = document.createElement('tr');

                    // Agregar celdas con los datos
                    const celdaNombre = document.createElement('td');
                    celdaNombre.textContent = curso.nombre_curso;

                    const celdaInscritos = document.createElement('td');
                    celdaInscritos.textContent = curso.alumnos_inscritos;

                    const celdaPromedio = document.createElement('td');
                    celdaPromedio.textContent = `${curso.promedio_avance}%`;

                    const celdaIngresos = document.createElement('td');
                    celdaIngresos.textContent = `$${curso.total_ingresos.toFixed(2)}`;

                    totalIngresosCursos += curso.total_ingresos;

                    // Añadir celdas a la fila
                    row.appendChild(celdaNombre);
                    row.appendChild(celdaInscritos);
                    row.appendChild(celdaPromedio);
                    row.appendChild(celdaIngresos);

                    // Añadir fila a la tabla
                    tablaCursos.appendChild(row);

                    // Obtener detalles por curso (segundo GET)
                    obtenerVentasPorCurso(curso.nombre_curso);
                });

                // Actualizar el total de ingresos en el pie de la tabla
                const cursosFooter = tablaCursos.closest('table').querySelector('tfoot td:last-child');
                cursosFooter.textContent = `$${totalIngresosCursos.toFixed(2)}`;
            } else {
                console.error('Error: Datos de cursos no válidos.');
            }
        })
        .catch(error => console.error('Error al obtener las ventas por curso:', error));

    // Función para obtener los detalles de un curso
    function obtenerVentasPorCurso(cursoTitulo) {
        const url = `${ventasPorCursoBaseURL}${encodeURIComponent(cursoTitulo)}`;
        fetch(url)
            .then(response => response.json())
            .then(alumnos => {
                if (Array.isArray(alumnos)) {
                    alumnos.forEach(alumno => {
                        // Crear fila para la tabla de alumnos
                        const row = document.createElement('tr');

                        // Agregar celdas con los datos
                        const celdaNombre = document.createElement('td');
                        celdaNombre.textContent = alumno.nombre_alumno;

                        const celdaFecha = document.createElement('td');
                        celdaFecha.textContent = alumno.fecha_inscripcion;

                        const celdaAvance = document.createElement('td');
                        celdaAvance.textContent = `${alumno.nivel_avance}%`;

                        const celdaPago = document.createElement('td');
                        celdaPago.textContent = `$${alumno.precio_pagado.toFixed(2)}`;

                        const celdaFormaPago = document.createElement('td');
                        celdaFormaPago.textContent = alumno.forma_pago;

                        // Añadir celdas a la fila
                        row.appendChild(celdaNombre);
                        row.appendChild(celdaFecha);
                        row.appendChild(celdaAvance);
                        row.appendChild(celdaPago);
                        row.appendChild(celdaFormaPago);

                        // Añadir fila a la tabla
                        tablaAlumnos.appendChild(row);
                    });

                    // Calcular y actualizar el total en el pie de la tabla
                    const totalIngresos = alumnos.reduce((sum, alumno) => sum + alumno.precio_pagado, 0);
                    const alumnosFooter = tablaAlumnos.closest('table').querySelector('tfoot td:last-child');
                    alumnosFooter.textContent = `$${totalIngresos.toFixed(2)}`;
                } else {
                    console.error('Error: Datos de alumnos no válidos.');
                }
            })
            .catch(error => console.error('Error al obtener los detalles por alumno:', error));
    }
});
