<?php
include("../../config/sessionVerif.php");

$usuario_id = $_SESSION['usuario_id'];

// Hacer una solicitud a la API 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/aprendi/api/usuariosController.php?id=" . $usuario_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

// Decodificar la respuesta de la API
$usuarioDatos = json_decode($response, true);

if (isset($usuarioDatos['status']) && $usuarioDatos['status'] === 'error') {
    header("Location: error.php");
    exit(); // Detener el script si hay un error
} else {
    $correo = $usuarioDatos['correo'];
    $rol = $usuarioDatos['rol'];
}

$rol_requerido = "instructor"; // El rol que puede acceder a esta página

if ($rol !== $rol_requerido) {
    header("location:../index/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <!-- Contenedor del Menú -->
    <div id="menu-container"></div>

    <button class="return" onclick="history.back()">Regresar</button>
    <div class="container mt-5">
        <h3 class="text-center">Reporte de Ventas</h3>

        <!-- Filtros -->
        <div class="filter-container mt-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="dateRange" class="form-label">Rango de fechas de creación:</label>
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
                        <input class="form-check-input" type="checkbox" id="activeCourses">
                        <label class="form-check-label" for="activeCourses">Solo cursos activos</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de ventas por curso -->
        <h5 class="text-center mb-4">Ventas por Curso</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre del curso</th>
                    <th>Alumnos inscritos</th>
                    <th>Nivel promedio cursado</th>
                    <th>Total de ingresos</th>
                </tr>
            </thead>
            <tbody>
                <!-- Curso 1 -->
                <tr>
                    <td>Curso de Programación en Python</td>
                    <td>150</td>
                    <td>75%</td>
                    <td>$15,000.00</td>
                </tr>
                <!-- Curso 2 -->
                <tr>
                    <td>Curso de Marketing Digital</td>
                    <td>85</td>
                    <td>60%</td>
                    <td>$8,500.00</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total de ingresos (Tarjeta de crédito):</strong></td>
                    <td>$18,000.00</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total de ingresos (Paypal):</strong></td>
                    <td>$5,500.00</td>
                </tr>
            </tfoot>
        </table>

        <!-- Detalle por alumno -->
        <h5 class="text-center mb-4">Detalle por Alumno</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre del alumno</th>
                    <th>Fecha de inscripción</th>
                    <th>Nivel de avance</th>
                    <th>Precio pagado</th>
                    <th>Forma de pago</th>
                </tr>
            </thead>
            <tbody>
                <!-- Alumno 1 -->
                <tr>
                    <td>Juan Pérez</td>
                    <td>01 Sep 2024</td>
                    <td>80%</td>
                    <td>$100.00</td>
                    <td>Tarjeta de crédito</td>
                </tr>
                <!-- Alumno 2 -->
                <tr>
                    <td>María López</td>
                    <td>05 Sep 2024</td>
                    <td>100%</td>
                    <td>$120.00</td>
                    <td>Paypal</td>
                </tr>
                <!-- Aquí se puede repetir según haya más alumnos -->
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total de ingresos (Tarjeta de crédito):</strong></td>
                    <td>$100.00</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total de ingresos (Paypal):</strong></td>
                    <td>$120.00</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total de ingresos:</strong></td>
                    <td>$220.00</td>
                </tr>
            </tfoot>
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
</body>
</html>
