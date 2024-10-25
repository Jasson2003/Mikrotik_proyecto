<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Aquí puedes implementar la verificación de usuario y contraseña
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verifica si las credenciales son correctas
    if ($username === 'admin' && $password === 'admin') { // Cambia esto según tus credenciales
        $_SESSION['loggedin'] = true;
        header("location: index.php");
        exit;
    } else {
        $login_err = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Iniciar Sesión</title>
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>
        <form action="" method="post">
            <label for="username">Usuario:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Iniciar Sesión">
        </form>
        <?php if (isset($login_err)) { echo "<div class='alert alert-error'>$login_err</div>"; } ?>
    </div>
</body>
</html>
