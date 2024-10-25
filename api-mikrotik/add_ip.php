<?php
session_start();
require('routeros_api.class.php');

$API = new RouterosAPI();

if (isset($_POST['ip']) && isset($_POST['interface'])) {
    $ip = $_POST['ip'];
    $interface = $_POST['interface'];

    // Intentar conectar a MikroTik
    if ($API->connect('192.168.3.146', 'admin', 'admin')) {
        // Preparar los datos para agregar la IP
        $data = [
            'address' => $ip,
            'interface' => $interface
        ];

        // Ejecutar el comando para agregar la IP
        $result = $API->comm('/ip/address/add', $data);
        
        // Verificar el resultado
        if ($result) {
            $_SESSION['message'] = "La IP $ip ha sido agregada correctamente a la interfaz $interface.";
            $_SESSION['alert_class'] = 'alert-success';
        } else {
            $_SESSION['message'] = "Error al agregar la IP $ip.";
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
