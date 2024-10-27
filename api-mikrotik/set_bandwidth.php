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

    if ($API->connect('192.168.3.151', 'admin', 'admin')) {
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
            'max-limit' => "{$upload_limit}k/{$download_limit}k"
        ]);

        $_SESSION['message'] = "Límite de ancho de banda establecido correctamente para $ip_bandwidth.";
        $_SESSION['alert_class'] = 'alert-success';
        $API->disconnect();
    } else {
        $_SESSION['message'] = "Error al conectar a MikroTik.";
        $_SESSION['alert_class'] = 'alert-danger';
    }

    header("Location: index.php");
    exit;
}
?>