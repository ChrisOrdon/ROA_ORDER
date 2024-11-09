<?php
session_start();
include 'conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$success_message = $error_message = '';

// Manejo de búsqueda de pedidos para edición
$pedidos_data = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar_pedido'])) {
    $nombre_cliente = $_POST['nombre_cliente'];
    $fecha_entrega = $_POST['fecha_entrega'];

    // Realizar la consulta para obtener los pedidos del cliente en una fecha específica
    $stmt = $conn->prepare("SELECT p.*, c.nombre AS cliente_nombre FROM pedidos p 
                            JOIN clientes c ON p.id_cliente = c.id_cliente 
                            WHERE c.nombre = ? AND p.fecha_entrega = ?");
    $stmt->bind_param("ss", $nombre_cliente, $fecha_entrega);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pedidos_data = $result->fetch_all(MYSQLI_ASSOC); // Trae todos los pedidos encontrados
    } else {
        $error_message = "No se encontraron pedidos para este cliente en la fecha seleccionada. Por favor, intenta de nuevo.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Pedido - ROA-Order</title>
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
            max-width: 800px;
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
        input[type="text"], input[type="date"], input[type="number"] {
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
    </style>
</head>
<body>
    <div class="container">
        <h3>Editar Pedido</h3>

        <!-- Formulario de búsqueda de pedidos -->
        <div class="card">
            <form action="editar_pedido.php" method="POST">
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
        </div>

        <!-- Mostrar mensaje de error si no se encontraron pedidos -->
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Mostrar los pedidos encontrados -->
        <?php if ($pedidos_data): ?>
        <div class="card">
            <h4>Pedidos encontrados para <?php echo $pedidos_data[0]['cliente_nombre']; ?> en la fecha <?php echo $fecha_entrega; ?></h4>
            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; margin-top: 20px;">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos_data as $pedido): ?>
                        <tr>
    <form action="editar_pedido.php" method="POST">
        <input type="hidden" name="id_pedido" value="<?php echo $pedido['id_pedido']; ?>">
        <td>
            <input type="text" name="producto" value="<?php echo $pedido['producto']; ?>" required>
        </td>
        <td>
            <input type="number" name="cantidad" value="<?php echo $pedido['cantidad']; ?>" required>
        </td>
        <td>
            <?php echo $pedido['subtotal']; ?>
        </td>
        <td>
            <button type="submit" name="editar_detalle" class="btn">Editar</button>
            <button type="submit" name="eliminar_detalle" class="btn">Eliminar</button>
        </td>
    </form>
</tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <button onclick="window.location.href='pedidos.php'" class="btn">Regresar</button>

        <!-- Mensajes de éxito -->
        <?php if (isset($success_message)) : ?>
            <p class="success-message"><?php echo $success_message; ?></p>
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