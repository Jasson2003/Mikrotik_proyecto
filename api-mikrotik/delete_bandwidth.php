<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

require('routeros_api.class.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];

    // Conectar al API de MikroTik
    $API = new RouterosAPI();
    if ($API->connect('192.168.3.151', 'admin', 'admin')) {
        // Buscar el límite de ancho de banda por nombre
        $bandwidthRecords = $API->comm('/queue/simple/print');
        foreach ($bandwidthRecords as $record) {
            if ($record['name'] == $name) {
                // Eliminar el límite de ancho de banda
                $API->comm('/queue/simple/remove', array(
                    'numbers' => $record['.id']
                ));
                $_SESSION['message'] = "Límite de ancho de banda eliminado exitosamente.";
                $_SESSION['alert_class'] = "success";
                break;
            }
        }
        $API->disconnect();
    } else {
        $_SESSION['message'] = "Error de conexión con MikroTik.";
        $_SESSION['alert_class'] = "error";
    }
}
header("location: index.php");
exit;
?>
