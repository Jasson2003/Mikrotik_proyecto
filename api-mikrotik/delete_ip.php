<?php
require('routeros_api.class.php');

if (isset($_POST['ip'])) {
    $ipAddress = $_POST['ip'];
    $API = new RouterosAPI();

    if ($API->connect('192.168.3.151', 'admin', 'admin')) {
        // Buscar la IP por dirección
        $addresses = $API->comm('/ip/address/print');
        foreach ($addresses as $address) {
            if ($address['address'] == $ipAddress) {
                // Eliminar la IP
                $API->comm('/ip/address/remove', array(
                    'numbers' => $address['.id'],
                ));
                break;
            }
        }
        $API->disconnect();
        echo "IP eliminada.";
    } else {
        echo "No se pudo conectar al router.";
    }
} else {
    echo "No se proporcionó ninguna IP.";
}
?>