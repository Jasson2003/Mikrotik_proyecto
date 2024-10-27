<?php
session_start();
require('routeros_api.class.php');

$API = new RouterosAPI();

if (isset($_POST['new_ip']) && isset($_POST['new_interface']) && isset($_POST['old_ip'])) {
    $new_ip = $_POST['new_ip'];
    $new_interface = $_POST['new_interface'];
    $old_ip = $_POST['old_ip'];

    // Intentar conectar a MikroTik
    if ($API->connect('192.168.3.151', 'admin', 'admin')) {
        // Preparar los datos para actualizar la IP
        $data = [
            'address' => $new_ip,
            'interface' => $new_interface,
            '.id' => $API->comm('/ip/address/print', ['?address' => $old_ip])[0]['.id']
        ];

        // Ejecutar el comando para actualizar la IP
        $result = $API->comm('/ip/address/set', $data);

        // Verificar el resultado
        if ($result) {
            $_SESSION['message'] = "La IP $old_ip ha sido actualizada a $new_ip en la interfaz $new_interface.";
            $_SESSION['alert_class'] = 'alert-success';
        } else {
            $_SESSION['message'] = "Error al actualizar la IP $old_ip.";
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
