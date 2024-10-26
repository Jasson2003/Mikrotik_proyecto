<?php
session_start();
require('routeros_api.class.php');

$API = new RouterosAPI();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_ip = $_POST['current_ip'];

    // Intentar conectar a MikroTik
    if ($API->connect('192.168.3.146', 'admin', 'admin')) {
        // Buscar la IP
        $addresses = $API->comm('/ip/address/print', ['?address' => $current_ip]);
        if (!empty($addresses)) {
            // Puedes almacenar los datos en $_SESSION para usarlos en index.php
            $_SESSION['ip_data'] = $addresses[0]; // Almacena el primer resultado
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['message'] = "No se encontró la IP $current_ip.";
            $_SESSION['alert_class'] = 'alert-error';
        }

        $API->disconnect();
    } else {
        $_SESSION['message'] = "No se pudo conectar a MikroTik.";
        $_SESSION['alert_class'] = 'alert-error';
    }

    header(header: "Location: index.php");
    exit();
}
?>