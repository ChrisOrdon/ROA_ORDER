<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pedidos - ROA-Order</title>
    <style>
        /* CSS para centrar y mejorar la presentación en modo oscuro */
        body {
            background-color: #2c3e50;
            color: #ecf0f1;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        .button {
            background-color: #2980b9;
            color: #ecf0f1;
            padding: 10px 20px;
            border: none;
            margin: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #3498db;
        }
    </style>
</head>
<body>
    <h1>Gestión de Pedidos</h1>
    <button onclick="window.location.href='crear_pedido.php'" class="button">Crear Pedido</button>
    <button onclick="window.location.href='editar_pedido.php'" class="button">Editar Pedido</button>
    <button onclick="window.location.href='eliminar_pedido.php'" class="button">Eliminar Pedido</button>
    <br><br>
    <button onclick="window.location.href='menu.php'" class="button">Regresar al Menú Principal</button>
</body>
</html>
