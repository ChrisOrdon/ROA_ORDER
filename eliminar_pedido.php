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

// Manejo de búsqueda de pedido para eliminar
$pedido_data = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar_pedido'])) {
    $nombre_cliente = $_POST['nombre_cliente'];
    $fecha_entrega = $_POST['fecha_entrega'];

    $stmt = $conn->prepare("SELECT * FROM pedidos WHERE nombre_cliente = ? AND fecha_entrega = ?");
    $stmt->bind_param("ss", $nombre_cliente, $fecha_entrega);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $pedido_data = $result->fetch_assoc();
    } else {
        $error_message = "Pedido no encontrado.";
    }
    $stmt->close();
}

// Manejo de eliminación de pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_pedido'])) {
    $id_pedido = $_POST['id_pedido'];

    // Eliminar los detalles del pedido
    $stmt = $conn->prepare("DELETE FROM detalles_pedido WHERE id_pedido = ?");
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    $stmt->close();

    // Eliminar el pedido
    $stmt = $conn->prepare("DELETE FROM pedidos WHERE id_pedido = ?");
    $stmt->bind_param("i", $id_pedido);
    if ($stmt->execute()) {
        $success_message = "Pedido eliminado exitosamente.";
    } else {
        $error_message = "Error al eliminar el pedido: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Pedido - ROA-Order</title>
    <style>
        /* Aquí puedes agregar tu estilo CSS */
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
    </style>
</head>
<body>
    <div class="container">
        <h3>Eliminar Pedido</h3>

        <!-- Botón de regresar al menú -->
        <button onclick="window.location.href='pedidos.php'" class="btn">Regresar</button>

        <!-- Formulario para buscar pedido -->
        <form action="eliminar_pedido.php" method="POST">
            <div class="input-field">
                <label for="nombre_cliente">Nombre del Cliente</label>
                <input type="text" id="nombre_cliente" name="nombre_cliente" required>
            </div>
            <div class="input-field">
                <label for="fecha_entrega">Fecha de Entrega</label>
                <input type="date" id="fecha_entrega" name="fecha_entrega" required>
            </div>
            <button type="submit" name="buscar_pedido" class="btn">Buscar Pedido</button>
        </form>

        <?php if ($pedido_data): ?>
            <h4>Datos del Pedido</h4>
            <p><strong>Nombre Cliente:</strong> <?php echo $pedido_data['nombre_cliente']; ?></p>
            <p><strong>Fecha de Entrega:</strong> <?php echo $pedido_data['fecha_entrega']; ?></p>

            <!-- Confirmación de eliminación -->
            <form action="eliminar_pedido.php" method="POST">
                <input type="hidden" name="id_pedido" value="<?php echo $pedido_data['id_pedido']; ?>">
                <p>¿Está seguro de que desea eliminar este pedido?</p>
                <button type="submit" name="eliminar_pedido" class="btn">Eliminar Pedido</button>
            </form>
        <?php endif; ?>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($success_message)) : ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php elseif (isset($error_message)) : ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #2C3E50; /* Fondo en color oscuro */
    color: #ECF0F1; /* Color de texto claro */
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Contenedor principal */
.container {
    background-color: #34495E; /* Color de fondo de los contenedores */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 800px;
}

/* Encabezado del formulario */
h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #E74C3C; /* Color de texto para los títulos */
}

/* Estilo para los formularios */
form {
    display: flex;
    flex-direction: column;
}

/* Estilo para los inputs */
input[type="text"], input[type="password"], input[type="date"], input[type="number"], select {
    padding: 10px;
    margin: 10px 0;
    border: none;
    border-radius: 5px;
    background-color: #BDC3C7;
    color: #2C3E50;
    font-size: 16px;
}

input[type="text"]:focus, input[type="password"]:focus, input[type="date"]:focus, input[type="number"]:focus, select:focus {
    border: 2px solid #E74C3C;
    outline: none;
}

/* Botones */
button {
    padding: 12px;
    background-color: #E74C3C; /* Rojo vibrante */
    border: none;
    border-radius: 5px;
    color: white;
    font-size: 16px;
    cursor: pointer;
    margin: 10px 0;
}

button:hover {
    background-color: #C0392B;
}

/* Estilo para las tablas */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #BDC3C7;
}

th {
    background-color: #34495E;
    color: #ECF0F1;
}

tr:nth-child(even) {
    background-color: #2C3E50;
}

tr:hover {
    background-color: #34495E;
}

/* Mensajes de error o éxito */
.error, .success {
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    text-align: center;
}

.error {
    background-color: #E74C3C;
    color: white;
}

.success {
    background-color: #27AE60;
    color: white;
}

/* Estilo para los botones de acción */
button.regresar {
    background-color: #3498DB;
}

button.regresar:hover {
    background-color: #2980B9;
}

/* Estilo para la sección de productos */
.productos-section {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.productos-section .producto {
    background-color: #BDC3C7;
    padding: 15px;
    border-radius: 8px;
    width: 30%;
    min-width: 250px;
}

.productos-section .producto h3 {
    text-align: center;
    color: #2C3E50;
}

.productos-section .producto input {
    width: 100%;
    margin: 10px 0;
}

/* Estilo para la opción de editar y eliminar */
.edit-delete-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.edit-delete-section button {
    width: 48%;
}

/* Estilo para las secciones del menú */
.menu-section {
    display: flex;
    justify-content: space-around;
    margin-top: 20px;
}

/* Estilo para los botones del menú */
.menu-section button {
    width: 30%;
    font-size: 16px;
    padding: 15px;
}

.menu-section button:hover {
    background-color: #2980B9;
}
</style>