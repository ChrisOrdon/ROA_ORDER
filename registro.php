<?php
// Iniciar sesión y conexión con la base de datos
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $correo, $password);

    if ($stmt->execute()) {
        echo "Registro exitoso. Ahora puedes <a href='index.php'>iniciar sesión</a>.";
    } else {
        echo "Error en el registro: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - ROA-Order</title>
    <style>
        /* Estilos en modo nocturno */
        body {
            background-color: #263238;
            color: #ffffff;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h3 {
            color: #ffffff;
        }
        .card {
            background-color: #37474f;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .input-field {
            margin-bottom: 15px;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #b0bec5;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 8px;
            border: none;
            border-radius: 4px;
            background-color: #546e7a;
            color: #ffffff;
        }
        .btn {
            background-color: #546e7a;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        .btn:hover {
            background-color: #455a64;
        }
        .success-message, .error-message {
            font-size: 14px;
        }
        .success-message {
            color: #81c784;
        }
        .error-message {
            color: #e57373;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Registro de Usuario</h3>
        <div class="card">
            <form action="registro.php" method="POST">
                <div class="input-field">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="input-field">
                    <label for="correo">Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                <div class="input-field">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Registrarse</button>
            </form>
        </div>
    </div>
</body>
</html>
