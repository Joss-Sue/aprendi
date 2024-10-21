<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kardex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <button class="return" onclick="history.back()">Regresar</button>
    <div class="container mt-5">
        <h3 class="text-center">Kardex de Cursos</h3>

        <!-- Filtros -->
        <div class="filter-container mt-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="dateRange" class="form-label">Rango de fechas de inscripción:</label>
                    <input type="date" id="startDate" class="form-control">
                    <input type="date" id="endDate" class="form-control mt-2">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="category" class="form-label">Categoría:</label>
                    <select class="form-select" id="category">
                        <option value="">Todas</option>
                        <option value="it">IT & Software</option>
                        <option value="marketing">Marketing</option>
                        <option value="design">Design</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Mostrar:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="completedCourses">
                        <label class="form-check-label" for="completedCourses">Solo cursos terminados</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="activeCourses">
                        <label class="form-check-label" for="activeCourses">Solo cursos activos</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla estilo Kardex -->
<!-- Tabla estilo Kardex -->
<table class="table table-bordered mt-4">
    <thead>
        <tr>
            <th>Nombre del curso</th>
            <th>Progreso</th>
            <th>Fecha de inscripción</th>
            <th>Último acceso</th>
            <th>Fecha de terminación</th>
            <th>Estado</th>
            <th>Certificado</th> <!-- Nueva columna -->
        </tr>
    </thead>
    <tbody>
        <!-- Curso 1 -->
        <tr>
            <td>Curso de Programación en Python</td>
            <td>75%</td>
            <td>2024-09-01</td>
            <td>2024-09-18</td>
            <td>-</td>
            <td>Incompleto</td>
            <td>-</td> <!-- Sin certificado -->
        </tr>
        <!-- Curso 2 -->
        <tr>
            <td>Curso de Marketing Digital</td>
            <td>100%</td>
            <td>2024-08-10</td>
            <td>2024-09-12</td>
            <td>2024-09-12</td>
            <td>Completo</td>
            <td>
                <button class="btn btn-green" onclick="descargarCertificado('Curso de Marketing Digital')">Descargar</button>
            </td> <!-- Botón para descargar certificado -->
        </tr>
    </tbody>
</table>

    </div>
    <!-- Contenedor del Footer -->
    <div id="footer-container"></div>
    <!-- Incluir el menú y el footer con JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../partials/menu.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('menu-container').innerHTML = data;
                });

            fetch('../partials/footer.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('footer-container').innerHTML = data;
                });

            // Funcionalidad para mostrar/ocultar buscador avanzado
            document.getElementById('advancedSearchToggle').addEventListener('click', function() {
                const advancedSearch = document.getElementById('advancedSearch');
                advancedSearch.style.display = (advancedSearch.style.display === 'none' || advancedSearch.style.display === '') ? 'block' : 'none';
            });
        });
    </script>
<script>
    async function descargarCertificado(nombreCurso) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape', 'mm', 'a4'); // Crear un PDF horizontal

        // Cargar la imagen de fondo
        const imageUrl = '../Imagenes/png certificado.png'; 
        const img = new Image();
        img.src = imageUrl;

        img.onload = function () {
            // Dibujar la imagen de fondo
            doc.addImage(img, 'PNG', 0, 0, 297, 210); // Ajustar tamaño y posición según tu diseño

            // Datos del certificado
            const nombreUsuario = "Nombre del Usuario"; // Esto debería ser dinámico, obteniendo el nombre del usuario actual
            const fecha = new Date().toLocaleDateString();

            // Contenido del certificado (superpuesto sobre el diseño)
            doc.setFontSize(20);
            doc.setFont('times', 'bold');
            doc.text(nombreUsuario, 148.5, 100, null, null, 'center'); // Ajusta la posición según tu diseño
            
            doc.setFontSize(16);
            doc.setFont('times', 'normal');
            doc.text(`Por haber completado satisfactoriamente el curso:`, 148.5, 120, null, null, 'center');
            
            doc.setFontSize(20);
            doc.setFont('times', 'bold');
            doc.text(nombreCurso, 148.5, 140, null, null, 'center');
            
            doc.setFontSize(16);
            doc.setFont('times', 'normal');
            doc.text(`Fecha de finalización: ${fecha}`, 148.5, 160, null, null, 'center');

            // Descargar el PDF
            doc.save(`Certificado_${nombreCurso}.pdf`);
        };
    }
</script>


    
</body>
</html>
