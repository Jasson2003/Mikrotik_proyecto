<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

require('routeros_api.class.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $ip_bandwidth = $_POST['ip_bandwidth'];
    $download_limit = $_POST['download_limit'];
    $upload_limit = $_POST['upload_limit'];

    $API = new RouterosAPI();

    if ($API->connect('192.168.3.146', 'admin', 'admin')) {
        // Limpiar cualquier regla anterior para la misma IP
        $existingQueues = $API->comm('/queue/simple/print', ['?target' => $ip_bandwidth]);

        // Si hay colas previas, eliminarlas
        if (!empty($existingQueues)) {
            foreach ($existingQueues as $queue) {
                // Asegúrate de eliminar las colas antiguas
                $API->comm('/queue/simple/remove', ['.id' => $queue['.id']]);
            }
        }

        // Crear una nueva cola simple
        $result = $API->comm('/queue/simple/add', [
            'name' => $name,
            'target' => $ip_bandwidth,
            'max-limit' => $download_limit . 'k/' . $upload_limit . 'k',
            'priority' => '8' // Ajusta la prioridad según lo necesites
        ]);

        if ($result) {
            $_SESSION['message'] = "Límite de ancho de banda actualizado para $ip_bandwidth.";
            $_SESSION['alert_class'] = "success";
        } else {
            $_SESSION['message'] = "Error al crear la cola de ancho de banda.";
            $_SESSION['alert_class'] = "error";
        }

        $API->disconnect();
    } else {
        $_SESSION['message'] = "Error al conectar con el router.";
        $_SESSION['alert_class'] = "error";
    }

    header("location: index.php");
    exit;
}
?>
