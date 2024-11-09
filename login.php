<?php
session_start();
include 'conexion.php'; // Incluye el archivo de conexión a la base de datos

// Comprueba si se envió el formulario por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene los valores del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];
    $captcha = $_POST['captcha'];

    // Verifica si el captcha es correcto
    if ($captcha != $_SESSION['captcha']) {
        echo "Captcha incorrecto.";
        exit;
    }

    // Consulta para buscar al usuario en la base de datos
    $sql = "SELECT * FROM usuarios WHERE nombre = '$username' AND contrasena = '$password'";
    $result = $conn->query($sql);

    // Si el usuario es encontrado, inicia sesión y redirige
    if ($result->num_rows > 0) {
        $_SESSION['loggedin'] = true;
        header("Location: menu.php");
    } else {
        echo "Nombre de usuario o contraseña incorrectos.";
    }
}
?>
