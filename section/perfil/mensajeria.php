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
}
$correo = $usuarioDatos['correo'];
$rol = $usuarioDatos['rol'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/index.css">
</head>
<style>
    .chat-box {
    height: 400px;
    overflow-y: scroll;
    border: 2px solid #D1C6B4;
    border-radius: 10px;
    padding: 10px;
}

.mensaje {
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 5px;
    max-width: 70%;
}

.mensaje-usuario {
    color: #ffff;
    background-color: #4db6ac; 
    align-self: flex-end; /* Alineado a la derecha */
}

.mensaje-instructor {
    background-color: #f1f1f1;
    align-self: flex-start; /* Alineado a la izquierda */
}

#mensajesContainer {
    max-height: 300px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    padding: 10px;
}
.textarea-btn{
    display: flex;
    justify-content: center;
    gap: 10px;
}
.btn-primary{
    background-color: #4db6ac;
    border: none;
}
.btn-primary:hover{
    background-color: #2e7b73;
    border: none;
}
.list-group{
    gap: 10px;
    border: none;
    border-radius: 20px;
}
.list-group-item{
    border-radius: 20px;
    font-weight: 600;
    color: #ffffff;
    background-color: #4db6ac;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    transition: all 0.3s ease;
}
.list-group-item:hover{
    color: #ffffff;
    background-color: #ff6f61;
    transform: scale(1.05); 
}
.list-group-item:active{
    color: white;
    background-color: #ff6f61;
}
.seleccionado {
    background-color: #ff6f61; 
    color: white; 
}
#sidebar{
    border-radius: 10px;
    background-color: #f2e5d0;
    border: 2px solid #D1C6B4;
}
</style>
<body>
    <div id="menu-container"></div>
    <button class="return" onclick="history.back()">Regresar</button>
    <div class="container mt-1">
        <div class="row">
            <!-- Panel de estudiantes/instructores según el rol -->
            <div class="col-md-4" id="sidebar">
                <h4 id="sidebarTitle">Usuarios</h4>
                <div id="usuariosContainer" class="list-group"></div>
            </div>

            <!-- Panel de chat -->
            <div class="col-md-8">
                <h4>Chat</h4>
                <div id="mensajesContainer" class="chat-box"></div>
                <form id="mensajeriaForm" class="mt-3">
                    <div class="textarea-btn">
                    <textarea class="form-control" id="mensaje" rows="3" placeholder="Escribe tu mensaje..."></textarea>
                    <button type="submit" class="btn btn-primary mt-2">Enviar</button>
                    </div>
                    <div id="mensajeError" style="color: red; margin-top: 5px;"></div>
                </form>
            </div>
        </div>
    </div>

    <div id="footer-container"></div>
    <script>
        const usuarioId = <?php echo json_encode($usuario_id); ?>;
        const usuarioRol = <?php echo json_encode($rol); ?>; // "instructor" o "estudiante"

        document.addEventListener('DOMContentLoaded', function() {
            // Cargar menú y footer
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
        });

    </script>
    <script src="../scriptJS/mensajeria-val.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

