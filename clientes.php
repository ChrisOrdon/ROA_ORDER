<?php
session_start();
include 'conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Variables de mensajes
$success_message = $error_message = '';

// Manejo del envío del formulario de agregar cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_cliente'])) {
    $nombre = $_POST['nombre'];
    $nit = $_POST['nit'];
    $direccion = $_POST['direccion'];
    $tipo_cliente = $_POST['tipo_cliente'];
    $telefono = $_POST['telefono'];

    $stmt = $conn->prepare("INSERT INTO clientes (nombre, nit, direccion, tipo_cliente, telefono) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $nit, $direccion, $tipo_cliente, $telefono);

    if ($stmt->execute()) {
        $success_message = "Cliente agregado exitosamente.";
    } else {
        $error_message = "Error al agregar cliente: " . $stmt->error;
    }
    $stmt->close();
}

// Manejo de búsqueda de cliente para edición
$client_data = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar_cliente'])) {
    $nombre_buscar = $_POST['nombre_buscar'];
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE nombre = ?");
    $stmt->bind_param("s", $nombre_buscar);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $client_data = $result->fetch_assoc();
    } else {
        $error_message = "Cliente no encontrado.";
    }
    $stmt->close();
}

// Manejo del envío del formulario de edición de cliente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_cliente'])) {
    $id_cliente = $_POST['id_cliente'];
    $nombre = $_POST['nombre'];
    $nit = $_POST['nit'];
    $direccion = $_POST['direccion'];
    $tipo_cliente = $_POST['tipo_cliente'];
    $telefono = $_POST['telefono'];

    $stmt = $conn->prepare("UPDATE clientes SET nombre = ?, nit = ?, direccion = ?, tipo_cliente = ?, telefono = ? WHERE id_cliente = ?");
    $stmt->bind_param("sssssi", $nombre, $nit, $direccion, $tipo_cliente, $telefono, $id_cliente);

    if ($stmt->execute()) {
        $success_message = "Cliente actualizado exitosamente.";
    } else {
        $error_message = "Error al actualizar el cliente: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Clientes - ROA-Order</title>
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
            max-width: 600px;
            text-align: center;
        }
        .card {
            background-color: #37474f;
            padding: 20px;
            margin-bottom: 20px;
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
        input[type="text"], input[type="number"] {
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
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #455a64;
        }
        .success-message, .error-message {
            font-size: 14px;
            color: #ffffff;
        }
        .success-message {
            color: #81c784;
        }
        .error-message {
            color: #e57373;
        }
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Gestión de Clientes</h3>

        <div class="action-buttons">
            <button onclick="showSection('agregar')" class="btn">Agregar Cliente</button>
            <button onclick="showSection('editar')" class="btn">Editar Cliente</button>
            <button onclick="window.location.href='menu.php'" class="btn">Regresar</button>
        </div>

        <!-- Sección para agregar cliente -->
        <div id="agregar" class="card" style="display: none;">
            <h4>Agregar Cliente</h4>
            <form action="clientes.php" method="POST">
                <div class="input-field">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="input-field">
                    <label for="nit">NIT</label>
                    <input type="text" id="nit" name="nit" required>
                </div>
                <div class="input-field">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" required>
                </div>
                <div class="input-field">
                    <label for="tipo_cliente">Tipo de Cliente</label>
                    <input type="text" id="tipo_cliente" name="tipo_cliente" required>
                </div>
                <div class="input-field">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" required>
                </div>
                <button type="submit" name="agregar_cliente" class="btn">Agregar Cliente</button>
            </form>
        </div>

        <!-- Sección para buscar y editar cliente -->
        <div id="editar" class="card" style="display: none;">
            <h4>Editar Cliente</h4>
            <form action="clientes.php" method="POST">
                <div class="input-field">
                    <label for="nombre_buscar">Nombre del Cliente</label>
                    <input type="text" id="nombre_buscar" name="nombre_buscar" required>
                </div>
                <button type="submit" name="buscar_cliente" class="btn">Buscar Cliente</button>
            </form>

            <?php if ($client_data): ?>
            <form action="clientes.php" method="POST">
                <input type="hidden" name="id_cliente" value="<?php echo $client_data['id_cliente']; ?>">
                <div class="input-field">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $client_data['nombre']; ?>" required>
                </div>
                <div class="input-field">
                    <label for="nit">NIT</label>
                    <input type="text" id="nit" name="nit" value="<?php echo $client_data['nit']; ?>" required>
                </div>
                <div class="input-field">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo $client_data['direccion']; ?>" required>
                </div>
                <div class="input-field">
                    <label for="tipo_cliente">Tipo de Cliente</label>
                    <input type="text" id="tipo_cliente" name="tipo_cliente" value="<?php echo $client_data['tipo_cliente']; ?>" required>
                </div>
                <div class="input-field">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo $client_data['telefono']; ?>" required>
                </div>
                <button type="submit" name="editar_cliente" class="btn">Actualizar Cliente</button>
            </form>
            <?php endif; ?>
        </div>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($success_message)) : ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)) : ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>

    <script>
        function showSection(section) {
            document.getElementById('agregar').style.display = 'none';
            document.getElementById('editar').style.display = 'none';
            document.getElementById(section).style.display = 'block';
        }
    </script>
</body>
</html>
