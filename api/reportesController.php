<?php
include "../models/reportesModel.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        {
            //http://localhost/aprendi/api/reportesController.php/?tipo=INSTRUCTOR&id=2&categoria=0&estado=1
            $reporte = null; 
            
            if (isset($_GET['tipo']) && $_GET['tipo'] == 'ADMIN') {
                if (isset($_GET['reporte'])) {
                    if ($_GET['reporte'] == 'INSTRUCTOR') {
                        $reporte = ReporteClass::obtenerReporteAdminIntructores();
                    } elseif ($_GET['reporte'] == 'ESTUDIANTE') {
                        $reporte = ReporteClass::obtenerReporteAdminEstudiantes();
                    }
                }
            } elseif (isset($_GET['tipo']) && $_GET['tipo'] == 'ESTUDIANTE') {
                if (isset($_GET['id'], $_GET['categoria'], $_GET['estado'])) {
                    $reporte = ReporteClass::obtenerKardex($_GET['id'], $_GET['categoria'], $_GET['estado']);
                }
            } elseif (isset($_GET['tipo']) && $_GET['tipo'] == 'INSTRUCTOR') {
                if (isset($_GET['id'], $_GET['categoria'], $_GET['estado'])) {
                    $reporte = ReporteClass::obtenerVentasCursos($_GET['id'], $_GET['categoria'], $_GET['estado']);
                } elseif (isset($_GET['curso_titulo'])) {
                    $reporte = ReporteClass::obtenerVentasPorCurso($_GET['curso_titulo']);
                }
            }
    
            // Respuesta final
            if ($reporte === null) {
                http_response_code(400);
                echo json_encode(array("status" => "error", "message" => "Ninguna categoría encontrada"));
            } else {
                http_response_code(200);
                echo json_encode($reporte);
            }
            exit;
        }
        break;    
    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method Not Allowed"));
        break;
}

?>