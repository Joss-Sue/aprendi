<!-- menu.html -->
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

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="cursosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-book"></i>Cursos
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="cursosDropdown">
                        <li><a class="dropdown-item" href="../cursos/RegistroCurso.php">Registrar Curso</a></li>
                        <li><a class="dropdown-item" href="../cursos/BajaCurso.php">Dar de Baja tu Curso</a></li>
                        <li><a class="dropdown-item" href="../cursos/mis-cursos.php">Mis Cursos</a></li>
                        <li><a class="dropdown-item" href="../index/index.php">Explorar Nuevos Cursos</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> Mi Perfil
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="perfilDropdown">
                        <li><a class="dropdown-item" href="../perfil/perfil.php">Mi Información</a></li>
                        <li><a class="dropdown-item" href="../perfil/kardex.php">Kardex</a></li>
                        <li><a class="dropdown-item" href="../perfil/ventas.php">Mis Ventas</a></li>
                        <li><a class="dropdown-item" href="../perfil/mensajeria.php">Mensajes</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../login/login.php"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
