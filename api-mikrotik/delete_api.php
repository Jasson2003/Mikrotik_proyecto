<?php
session_start();
require('routeros_api.class.php');

$API = new RouterosAPI();

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Intentar conectar a MikroTik
    if ($API->connect('192.168.3.146', 'admin', 'admin')) {
        // Ejecutar el comando para eliminar la IP
        $result = $API->comm('/ip/address/remove', ['.id' => $id]);
        
        // Verificar el resultado
        if ($result) {
            $_SESSION['message'] = "La IP ha sido eliminada correctamente.";
            $_SESSION['alert_class'] = 'alert-success';
        } else {
            $_SESSION['message'] = "Error al eliminar la IP.";
            $_SESSION['alert_class'] = 'alert-error';
        }

        $API->disconnect();
    } else {
        $_SESSION['message'] = "No se pudo conectar a MikroTik.";
        $_SESSION['alert_class'] = 'alert-error';
    }

    header("Location: index.php");
    exit();
}
?>
