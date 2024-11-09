<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú - ROA-Order</title>
    <style>
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
            text-align: center;
        }
        nav {
            background-color: #37474f;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        nav a {
            color: #ffffff;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
        }
        nav a:hover {
            color: #b0bec5;
        }
        h3 {
            color: #ffffff;
        }
    </style>
</head>
<link rel="stylesheet" href="chatbot/chatbot.css">
<body>
        <!-- Botón para abrir el chatbot -->
<button id="chatbotButton" onclick="openChat()">Chat</button>

<!-- Ventana del chatbot -->
<div id="chatbox" class="chatbox">
    <div class="chatlogs" id="chatlogs"></div>
    <input type="text" id="userMessage" placeholder="Escribe tu mensaje..." onkeyup="sendMessage(event)">
    <button onclick="sendMessage()" id="sendButton">Enviar</button>
</div>
    <div class="container">
        <nav>
            <a href="pedidos.php">Pedidos</a>
            <a href="clientes.php">Clientes</a>
            <a href="productos.php">Productos</a>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
        <h3>Bienvenido, <?php echo $_SESSION['usuario']; ?></h3>
        <p>Selecciona una opción del menú para continuar.</p>
    </div>
    <script src="chatbot/chatbot.js"></script>
</body>
</html>
