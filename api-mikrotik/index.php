<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>MikroTik API</title>
</head>
<body>
    <div class="container">
        <h1>Gestión de IPs en MikroTik</h1>
        <a href="logout.php">Cerrar sesión</a>
        
        <div class="tabs">
            <button class="tablink" onclick="openTab(event, 'addIp')">Agregar IP</button>
            <button class="tablink" onclick="openTab(event, 'viewIp')">Ver/Actualizar IP</button>
        </div>

        <div id="addIp" class="tabcontent">
            <h2>Agregar IP</h2>
            <?php
            if (isset($_SESSION['message'])) {
                echo "<div class='alert " . $_SESSION['alert_class'] . "'>" . $_SESSION['message'] . "</div>";
                unset($_SESSION['message']);
                unset($_SESSION['alert_class']);
            }
            ?>
            <form action="add_ip.php" method="post">
                <label for="ip">Dirección IP:</label>
                <input type="text" id="ip" name="ip" required>

                <label for="interface">Interfaz:</label>
                <input type="text" id="interface" name="interface" required>

                <input type="submit" value="Agregar IP">
            </form>
        </div>

        <div id="viewIp" class="tabcontent" style="display:none;">
            <h2>Ver/Actualizar IP</h2>
            <form action="view_ip.php" method="post">
                <label for="current_ip">Dirección IP actual:</label>
                <input type="text" id="current_ip" name="current_ip" required>

                <input type="submit" value="Buscar">
            </form>
            <div id="ipDetails" style="display:none;">
                <h3>Detalles de la IP</h3>
                <form action="update_ip.php" method="post">
                    <label for="new_ip">Nueva Dirección IP:</label>
                    <input type="text" id="new_ip" name="new_ip" required>

                    <label for="new_interface">Nueva Interfaz:</label>
                    <input type="text" id="new_interface" name="new_interface" required>

                    <input type="hidden" name="old_ip" id="old_ip">
                    <input type="submit" value="Actualizar IP">
                </form>
            </div>
        </div>

        <h2>Lista de IPs Registradas</h2>
        <table>
            <thead>
                <tr>
                    <th>Dirección IP</th>
                    <th>Interfaz</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require('routeros_api.class.php');
                $API = new RouterosAPI();

                if ($API->connect('192.168.3.146', 'admin', 'admin')) {
                    $addresses = $API->comm('/ip/address/print');
                    foreach ($addresses as $address) {
                        echo "<tr>";
                        echo "<td>" . $address['address'] . "</td>";
                        echo "<td>" . $address['interface'] . "</td>";
                        echo "<td><button onclick=\"setIpData('" . $address['address'] . "', '" . $address['interface'] . "')\">Editar</button></td>";
                        echo "</tr>";
                    }
                    $API->disconnect();
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        function setIpData(ip, interface) {
            document.getElementById('new_ip').value = ip;
            document.getElementById('new_interface').value = interface;
            document.getElementById('old_ip').value = ip; // Guardar IP antigua
            document.getElementById('ipDetails').style.display = 'block';
        }
    </script>
</body>
</html>
