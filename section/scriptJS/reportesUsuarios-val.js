document.addEventListener('DOMContentLoaded', function () {
    cargarReporteUsuarios();

    document.getElementById('reporteTipo').addEventListener('change', function () {
        cargarReporteUsuarios();
    });
});

function cargarReporteUsuarios() {
    const tipoReporte = document.getElementById('reporteTipo').value;
    const url = `http://localhost/aprendi/api/reportesController.php?tipo=ADMIN&reporte=${tipoReporte.toUpperCase()}`;

    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error("Error al obtener los usuarios");
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            if (tipoReporte === 'INSTRUCTOR') {
                document.getElementById('reporteInstructores').style.display = 'block';
                document.getElementById('reporteEstudiantes').style.display = 'none';
                llenarTablaInstructores(data);
            } else if (tipoReporte === 'ESTUDIANTE') {
                document.getElementById('reporteInstructores').style.display = 'none';
                document.getElementById('reporteEstudiantes').style.display = 'block';
                llenarTablaEstudiantes(data);
            }
        })
        .catch(error => {
            console.error('Error al cargar los usuarios:', error);
        });
}

function llenarTablaInstructores(instructores) {
    const tablaInstructores = document.getElementById('tablaInstructores');
    tablaInstructores.innerHTML = '';

    instructores.forEach(instructor => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${instructor.usuario}</td>
            <td>${instructor.nombre}</td>
            <td>${instructor.fecha_ingreso}</td>
            <td>${instructor.cantidad_cursos_ofrecidos}</td>
            <td>${instructor.total_ganancias}</td>
        `;
        tablaInstructores.appendChild(fila);
    });
}

function llenarTablaEstudiantes(estudiantes) {
    const tablaEstudiantes = document.getElementById('tablaEstudiantes');
    tablaEstudiantes.innerHTML = '';

    console.log(estudiantes);
    estudiantes.forEach(estudiante => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${estudiante.usuario}</td>
            <td>${estudiante.nombre}</td>
            <td>${estudiante.fecha_ingreso}</td>
            <td>${estudiante.cursos_inscritos}</td>
            <td>${Math.round(estudiante.porcentaje_cursos_terminados)}%</td>
        `;
        tablaEstudiantes.appendChild(fila);
    });
}

function filterUsers() {
    const searchValue = document.getElementById('searchUser').value.toLowerCase();
    const tablaInstructores = document.getElementById('tablaInstructores');
    const tablaEstudiantes = document.getElementById('tablaEstudiantes');

    const tipoReporte = document.getElementById('reporteTipo').value;

    let filas;

    if (tipoReporte === 'INSTRUCTOR') {
        filas = tablaInstructores.getElementsByTagName('tr');
    } else {
        filas = tablaEstudiantes.getElementsByTagName('tr');
    }

    for (let i = 0; i < filas.length; i++) {
        const celdas = filas[i].getElementsByTagName('td');
        if (celdas.length > 0) {
            const nombre = celdas[1].textContent || celdas[1].innerText;
            const correo = celdas[0].textContent || celdas[0].innerText;

            if (nombre.toLowerCase().indexOf(searchValue) > -1 || correo.toLowerCase().indexOf(searchValue) > -1) {
                filas[i].style.display = '';
            } else {
                filas[i].style.display = 'none';
            }
        }
    }
}

