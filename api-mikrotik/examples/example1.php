<?php

require('routeros_api.class.php'); // Ajusta la ruta si es necesario

$API = new RouterosAPI();

$API->debug = true; // Activa el modo debug para ver detalles

// Conéctate usando la IP, usuario y contraseña correctos
if ($API->connect('192.168.3.146', 'admin', 'admin')) {

    // Ejecuta el comando para obtener las interfaces
    $interface = $API->comm('/interface/print');
    
    // Convierte el resultado en JSON y lo imprime
    $result = json_encode($interface, JSON_PRETTY_PRINT);
    echo $result;

    // Desconéctate de la API
    $API->disconnect();

} else {
    echo "No se pudo conectar al dispositivo MikroTik.";
}

?>
