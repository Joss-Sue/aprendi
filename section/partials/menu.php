
<?php
// Incluye el archivo de verificación de sesión (asume que está en una ubicación accesible)
include_once("../../config/sessionVerif.php");

// Obtener el id del usuario de la sesión
$usuario_id = $_SESSION['usuario_id'];

// Hacer una solicitud a la API para obtener los datos del usuario por su ID
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/aprendi/api/usuariosController.php?id=" . $usuario_id);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

// Decodificar la respuesta de la API (asumiendo que devuelve un JSON)
$usuarioDatos = json_decode($response, true);

// Verificar si la API devolvió los datos correctamente
if (isset($usuarioDatos['status']) && $usuarioDatos['status'] === 'error') {
    echo "Error al obtener los datos del usuario: " . $usuarioDatos['message'];
} else {
    $correo = $usuarioDatos['correo'];
    $rol = $usuarioDatos['rol'];
}
?>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index/index.php">
            <img src="../Imagenes/APRENDIV2.png" alt="Logo" style="height: 20px;"> Cursos Online
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Nueva sección Funciones Adm -->
            <?php if ($rol == 'administrador'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="funcionesAdmDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Funciones Adm
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="funcionesAdmDropdown">
                        <li><a class="dropdown-item" href="../admin/RegistroCategorias.php">Registro de Categorías</a></li>
                        <li><a class="dropdown-item" href="../admin/ReporteUsuarios.php">Reporte de Usuarios</a></li>
                        <li><a class="dropdown-item" href="../admin/BloquearUsuarios.php">Bloquear Usuario</a></li>
                    </ul>
                </li>
            <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="cursosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-book"></i>Cursos
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="cursosDropdown">
                        <?php if($rol == 'instructor'): ?>
                        <li><a class="dropdown-item" href="../cursos/RegistroCurso.php">Registrar Curso</a></li>
                        <?php endif; ?>
                        <?php if ($rol == 'estudiante'): ?>
                        <li><a class="dropdown-item" href="../cursos/BajaCurso.php">Dar de Baja tu Curso</a></li>
                        <li><a class="dropdown-item" href="../cursos/mis-cursos.php">Mis Cursos</a></li>
                        <?php endif; ?>
                        <?php if ($rol == 'estudiante'|| $rol == 'instructor'||$rol == 'administrador' ): ?>
                        <li><a class="dropdown-item" href="../index/index.php">Explorar Nuevos Cursos</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> Mi Perfil
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="perfilDropdown">
                        <?php if ($rol == 'estudiante'||$rol == 'instructor'||$rol == 'administrador' ): ?>
                        <li><a class="dropdown-item" href="../perfil/perfil.php">Mi Información</a></li>
                        <?php endif; ?>
                        <?php if ($rol == 'estudiante'): ?>
                        <li><a class="dropdown-item" href="../perfil/kardex.php">Kardex</a></li>
                        <?php endif; ?>
                        <?php if($rol == 'instructor'): ?>
                        <li><a class="dropdown-item" href="../perfil/ventas.php">Mis Ventas</a></li>
                        <?php endif; ?>
                        <?php if ($rol == 'estudiante'||$rol == 'instructor'): ?>
                        <li><a class="dropdown-item" href="../perfil/mensajeria.php">Mensajes</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php if (!isset($_SESSION['usuario_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="../login/login.php"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="../../config/cerrarSesion.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
                </li>
            <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>