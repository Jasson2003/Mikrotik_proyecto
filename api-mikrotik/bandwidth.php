<?php
session_start();
require('routeros_api.class.php');

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ip = $_POST['ip'];
    $download = $_POST['download'];
    $upload = $_POST['upload'];

    $API = new RouterosAPI();
    if ($API->connect('192.168.3.146', 'admin', 'admin')) {
        $API->comm("/queue/simple/add", array(
            "name" => $ip,
            "target" => $ip,
            "max-limit" => "{$upload}k/{$download}k"
        ));
        $API->disconnect();
        header('Location: index.php');
    }
}
?>