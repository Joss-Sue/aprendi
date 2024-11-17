document.addEventListener('DOMContentLoaded', function () {
    cargarCategorias(); 
    cargarKardex(estudianteId);

    const soloCompletadosCheckbox = document.getElementById('completedCourses');
    const soloActivosCheckbox = document.getElementById('activeCourses');

    soloCompletadosCheckbox.addEventListener('change', function() {
        if (this.checked) {
            soloActivosCheckbox.checked = false; 
        }
    });

    soloActivosCheckbox.addEventListener('change', function() {
        if (this.checked) {
            soloCompletadosCheckbox.checked = false; 
        }
    });
    document.getElementById('completedCourses').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('activeCourses').disabled = true;
        } else {
            document.getElementById('activeCourses').disabled = false;
        }
    });
    
    document.getElementById('activeCourses').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('completedCourses').disabled = true;
        } else {
            document.getElementById('completedCourses').disabled = false;
        }
    });
    

    document.getElementById('filtroForm').addEventListener('input', () => aplicarFiltro());
    //limpiar filtros
    document.getElementById('clearFilters').addEventListener('click', limpiarFiltros);
    //

});


function cargarCategorias() {
    fetch('http://localhost/aprendi/api/categoriaController.php', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(categorias => {
        const comboBox = document.getElementById('category');
        comboBox.innerHTML = '<option value="">Todas</option>';

        categorias.forEach(categoria => {
            const option = document.createElement('option');
            option.value = categoria.id;
            option.textContent = categoria.nombre;
            comboBox.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error al cargar las categorías:', error.message);
    });
}

let cursosCargados = [];

async function cargarKardex(estudianteId) {
    try {
        const response = await fetch(`http://localhost/aprendi/api/inscripcionesController.php?id=${estudianteId}`);
        if (!response.ok) {
            throw new Error("Error al obtener los cursos inscritos");
        }
        cursosCargados = await response.json();
        aplicarFiltro();
    } catch (error) {
        console.error("Error al cargar el kardex de cursos:", error);
    }
}

async function aplicarFiltro() {
    const categoriaId = document.getElementById('category').value;
    const fechaInicio = document.getElementById('startDate').value;
    const fechaFin = document.getElementById('endDate').value;
    const soloCompletados = document.getElementById('completedCourses').checked;
    const soloActivos = document.getElementById('activeCourses').checked;

    const fechaInicioDate = fechaInicio ? new Date(fechaInicio) : null;
    const fechaFinDate = fechaFin ? new Date(fechaFin) : null;

    const kardexContainer = document.getElementById('kardexContainer');
    kardexContainer.innerHTML = '';

    for (let curso of cursosCargados) {
        // Obtener detalles del curso
        const cursoId = curso.curso_id;
        const urlCurso = `http://localhost/aprendi/api/cursoController.php?id=${cursoId}`;
        const urlProgreso = `http://localhost/aprendi/api/inscripcionesController.php?curso_id=${cursoId}&estudiante_id=${estudianteId}`;

        const [cursoDetallesResponse, progresoResponse] = await Promise.all([
            fetch(urlCurso),
            fetch(urlProgreso)
        ]);

        if (!cursoDetallesResponse.ok || !progresoResponse.ok) {
            console.error(`Error al obtener detalles o progreso para el curso con ID: ${cursoId}`);
            continue;
        }

        const cursoDetalles = await cursoDetallesResponse.json();
        const progresoData = await progresoResponse.json();

        const nombreCurso = cursoDetalles.titulo || 'Curso sin nombre';
        const progreso = progresoData && progresoData.progreso_curso ? parseFloat(progresoData.progreso_curso) : 0;

        let mostrarCurso = true;

        // Filtrar por categoría
        if (categoriaId && cursoDetalles.categoria_id != categoriaId) {
            mostrarCurso = false;
        }

        // Filtrar por fechas de inscripción
        if (fechaInicio) {
            const fechaInicioDate = new Date(fechaInicio);
            fechaInicioDate.setHours(0, 0, 0, 0); 

            const fechaInscripcion = new Date(curso.fecha_inscripcion);
            fechaInscripcion.setHours(0, 0, 0, 0); 

            if (fechaInscripcion.getTime() < fechaInicioDate.getTime()) {
                mostrarCurso = false; 
            }
        }

        if (fechaFin) {
            const fechaFinDate = new Date(fechaFin);
            fechaFinDate.setHours(24, 59, 59, 999); 

            const fechaInscripcion = new Date(curso.fecha_inscripcion);
            fechaInscripcion.setHours(0, 0, 0, 0); 

            if (fechaInscripcion.getTime() > fechaFinDate.getTime()) {
                mostrarCurso = false; 
            }
        }

        // Filtrar solo cursos completados o solo activos
        if (soloCompletados && progreso < 100) {
            mostrarCurso = false;
        }

        if (soloActivos && progreso === 100) {
            mostrarCurso = false;
        }

        // Si el curso pasa todos los filtros, agregar la fila al kardex
        if (mostrarCurso) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${nombreCurso}</td>
                <td>${progreso}%</td>
                <td>${curso.fecha_inscripcion}</td>
                <td>${curso.fecha_ultima || '-'}</td>
                <td>${progreso === 100 ? curso.fecha_terminacion : '-'}</td>
                <td>${progreso === 100 ? 'Completo' : 'Incompleto'}</td>
                <td>${progreso === 100 ? '<button class="btn btn-green" id="valorar" onclick="descargarCertificado(' + cursoId + ', ' + estudianteId + ')">Descargar</button>' : '-'}</td>
            `;
            kardexContainer.appendChild(row);
        }
    }
}

async function descargarCertificado(cursoId, estudianteId) {
    try {
        // Paso 1: Verificar si ya existe el certificado
        console.log("Verificando si ya existe el certificado para cursoId:", cursoId, "estudianteId:", estudianteId);
        
        let certificadoData = null;
        const responseCheck = await fetch(`http://localhost/aprendi/api/certificadosController.php?estudiante_id=${estudianteId}&curso_id=${cursoId}`);
        
        if (responseCheck.ok) {
            // Si la respuesta es correcta, obtener los datos del certificado
            certificadoData = await responseCheck.json();
        } else {
            const errorData = await responseCheck.json();
            console.error("Error al verificar el certificado:", errorData.message);

            // error indica que no hay certificado, intentamos registrar uno nuevo
            if (errorData.status === "error" && errorData.message === "ningun certificado encontrado") {
                console.log("Certificado no encontrado, registrando nuevo certificado.");

                const responseInsert = await fetch('http://localhost/aprendi/api/certificadosController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        curso_id: cursoId,
                        estudiante_id: estudianteId,
                    })
                });

                if (!responseInsert.ok) {
                    const errorText = await responseInsert.text();
                    console.error("Error al registrar el certificado:", errorText);
                    throw new Error("Error al registrar el certificado");
                }

                console.log("Certificado registrado con éxito");

                // Obtener los datos del certificado registrado
                const responseGet = await fetch(`http://localhost/aprendi/api/certificadosController.php?estudiante_id=${estudianteId}&curso_id=${cursoId}`);

                if (!responseGet.ok) {
                    const errorText = await responseGet.text();
                    console.error("Error al obtener los datos del certificado después del registro:", errorText);
                    throw new Error("Error al obtener los datos del certificado después del registro");
                }

                certificadoData = await responseGet.json();
            } else {
                // Si el error no tiene que ver con un certificado inexistente, lanzar error
                throw new Error("Error inesperado al verificar el certificado");
            }
        }

        // Paso 2: Crear el certificado en PDF con los datos obtenidos
        if (certificadoData) {
            const { nombre_estudiante, nombre_curso, fecha_emision, nombre_instructor } = certificadoData;

            // Crear el certificado en PDF con jsPDF
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('landscape', 'mm', 'a4'); // Crear un PDF horizontal

            // Cargar la imagen de fondo
            const imageUrl = '../Imagenes/png certificado.png';
            const img = new Image();
            img.src = imageUrl;

            img.onload = function () {
                // Dibujar la imagen de fondo
                doc.addImage(img, 'PNG', 0, 0, 297, 210); // Ajustar tamaño y posición

                // Contenido del certificado 
                doc.setFontSize(20);
                doc.setFont('times', 'bold');
                doc.text(nombre_estudiante, 148.5, 100, null, null, 'center');

                doc.setFontSize(16);
                doc.setFont('times', 'normal');
                doc.text(`Por haber completado satisfactoriamente el curso:`, 148.5, 120, null, null, 'center');

                doc.setFontSize(20);
                doc.setFont('times', 'bold');
                doc.text(nombre_curso, 148.5, 140, null, null, 'center');

                doc.setFontSize(16);
                doc.setFont('times', 'normal');
                doc.text(`Fecha de emisión: ${fecha_emision}`, 148.5, 160, null, null, 'center');
                doc.text(`Instructor: ${nombre_instructor}`, 148.5, 180, null, null, 'center');

                // Descargar el PDF
                doc.save(`Certificado_${nombre_curso}.pdf`);
            };
        }
    } catch (error) {
        console.error("Error al descargar el certificado:", error);
    }
}

// Función para limpiar los filtros
function limpiarFiltros() {
    document.getElementById('completedCourses').checked = '';
    document.getElementById('completedCourses').disabled = false;
    document.getElementById('activeCourses').checked = '';
    document.getElementById('activeCourses').disabled = false;
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('category').value = '';
    cargarKardex(estudianteId);
}
