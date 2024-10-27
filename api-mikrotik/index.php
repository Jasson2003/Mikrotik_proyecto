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
            <button class="tablink" onclick="openTab(event, 'bandwidthControl')">Control de Ancho de Banda</button>
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
                <label for="ip">Dirección IP (con máscara "/"):</label>
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

        <div id="bandwidthControl" class="tabcontent" style="display:none;">
            <h2>Control de Ancho de Banda</h2>
            <form action="set_bandwidth.php" method="post">
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" required>

                <label for="ip_bandwidth">Dirección IP (sin máscara "/"):</label>
                <input type="text" id="ip_bandwidth" name="ip_bandwidth" required>

                <label for="download_limit">Límite de Descarga (bits/s):</label>
                <input type="number" id="download_limit" name="download_limit" required min="1">

                <label for="upload_limit">Límite de Subida (bits/s):</label>
                <input type="number" id="upload_limit" name="upload_limit" required min="1">

                <input type="submit" value="Establecer Límite">
            </form>

            <h2>Lista de Ancho de Banda Registrada</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Dirección IP</th>
                        <th>Max / Limit</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require('routeros_api.class.php');
                    $API = new RouterosAPI();

                    if ($API->connect('192.168.3.151', 'admin', 'admin')) {
                        $bandwidthRecords = $API->comm('/queue/simple/print'); // Comando para obtener los registros de ancho de banda
                        foreach ($bandwidthRecords as $record) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($record['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($record['target']) . "</td>";
                            echo "<td>" . htmlspecialchars($record['max-limit']) . "</td>";
                            echo "<td>" . htmlspecialchars($record['max-limit']) . "</td>";
                            echo "<td>
                                    <button class='button-edit' onclick=\"editBandwidth('" . htmlspecialchars($record['name']) . "', '" . htmlspecialchars($record['target']) . "', '" . htmlspecialchars($record['max-limit']) . "');\">Editar</button>
                                    <button class='button-delete' onclick=\"deleteBandwidth('" . htmlspecialchars($record['name']) . "');\">Eliminar</button>
                                  </td>";
                            echo "</tr>";
                        }
                        $API->disconnect();
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <h2>Lista de IPs Registradas</h2>
        <table>
            <thead>
                <tr>
                    <th>Dirección IP</th>
                    <th>Interfaz</th>
                    <th> </th>
                    <th> </th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($API->connect('192.168.3.151', 'admin', 'admin')) {
                    $addresses = $API->comm('/ip/address/print');
                    foreach ($addresses as $address) {
                        echo "<tr>";
                        echo "<td>" . $address['address'] . "</td>";
                        echo "<td>" . $address['interface'] . "</td>";
                        // Botón "Editar"
                        echo "<td><button class='button-edit' onclick=\"setIpData('" . $address['address'] . "', '" . $address['interface'] . "'); openTab(event, 'viewIp');\">Editar</button></td>";
                        // Botón "Eliminar"
                        echo "<td><button class='button-delete' onclick=\"deleteIp('" . $address['address'] . "');\">Eliminar</button></td>";
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
                tabcontent[i].style.display = "none"; // Ocultar todos los contenidos
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block"; // Mostrar el contenido seleccionado
            evt.currentTarget.className += " active"; // Marcar como activo
        }

        function setIpData(ip, interface) {
            document.getElementById('new_ip').value = ip;
            document.getElementById('new_interface').value = interface;
            document.getElementById('old_ip').value = ip; // Guardar IP antigua
            document.getElementById('ipDetails').style.display = 'block';
        }

        function editBandwidth(name, ip, limit) {
            document.getElementById('name').value = name;
            document.getElementById('ip_bandwidth').value = ip;
            document.getElementById('download_limit').value = limit; // Asumimos que el límite de descarga y subida son iguales
            document.getElementById('upload_limit').value = limit; // Asumimos que el límite de descarga y subida son iguales
        }

        function deleteIp(ipAddress) {
            if (confirm("¿Estás seguro de que deseas eliminar esta IP?")) {
                // Realizar la petición AJAX para eliminar la IP
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_ip.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        alert("IP eliminada exitosamente.");
                        location.reload(); // Recargar la página para actualizar la lista
                    } else {
                        alert("Error al eliminar la IP: " + xhr.responseText);
                    }
                };
                xhr.send("ip=" + encodeURIComponent(ipAddress));
            }
        }

        function deleteBandwidth(bandwidthName) {
            if (confirm("¿Estás seguro de que deseas eliminar este límite de ancho de banda?")) {
                // Realizar la petición AJAX para eliminar el límite de ancho de banda
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_bandwidth.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        alert("Límite de ancho de banda eliminado exitosamente.");
                        location.reload(); // Recargar la página para actualizar la tabla
                    } else {
                        alert("Error al eliminar el límite de ancho de banda: " + xhr.responseText);
                    }
                };
                xhr.send("name=" + encodeURIComponent(bandwidthName));
            }
        }
    </script>
</body>
</html>
