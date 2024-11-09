<?php
session_start();
include 'conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$success_message = $error_message = '';

// Manejo de búsqueda de cliente
$client_data = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar_cliente'])) {
    $nombre_buscar = $_POST['nombre_buscar'];
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE nombre LIKE ?");
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

// Manejo del envío del formulario de crear pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_pedido'])) {
    $id_cliente = $_POST['id_cliente'];
    $fecha_entrega = $_POST['fecha_entrega'];

    // Obtener los productos y cantidades
    $productos = $_POST['producto'];
    $cantidades = $_POST['cantidad'];
    $total = 0;

    // Preparar los datos para insertar en la base de datos
    $stmt = $conn->prepare("INSERT INTO pedidos (id_cliente, fecha_entrega) VALUES (?, ?)");
    $stmt->bind_param("is", $id_cliente, $fecha_entrega);
    if ($stmt->execute()) {
        $id_pedido = $stmt->insert_id;  // Obtener el ID del pedido insertado
        $stmt->close();

        // Insertar los productos en la tabla de detalles de pedidos
        foreach ($productos as $index => $producto_id) {
            $cantidad = (int)$cantidades[$index];  // Asegurarse de que la cantidad sea un número
            if ($cantidad > 0) {
                $stmt = $conn->prepare("INSERT INTO detalles_pedido (id_pedido, id_producto, cantidad) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $id_pedido, $producto_id, $cantidad);
                $stmt->execute();
                $stmt->close();
                
                // Obtener el precio del producto
                $stmt = $conn->prepare("SELECT precio_unitario FROM productos WHERE id_producto = ?");
                $stmt->bind_param("i", $producto_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $producto = $result->fetch_assoc();
                    $precio_unitario = $producto['precio_unitario'];
                    $total += $precio_unitario * $cantidad;  // Calcular el subtotal por producto
                }
                $stmt->close();
            }
        }

        // Actualizar el total en el pedido
        $stmt = $conn->prepare("UPDATE pedidos SET total = ? WHERE id_pedido = ?");
        $stmt->bind_param("di", $total, $id_pedido);
        $stmt->execute();
        $stmt->close();

        $success_message = "Pedido creado exitosamente.";
    } else {
        $error_message = "Error al crear el pedido: " . $stmt->error;
    }
}

// Obtener los productos disponibles
$stmt = $conn->prepare("SELECT * FROM productos");
$stmt->execute();
$productos_result = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Pedido - ROA-Order</title>
    <style>
        /* Estilos similares a los anteriores */
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
        input[type="text"], input[type="number"], input[type="date"] {
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
        <h3>Crear Pedido</h3>

        <!-- Formulario de búsqueda de cliente -->
        <div class="card">
            <h4>Buscar Cliente</h4>
            <form action="crear_pedido.php" method="POST">
                <div class="input-field">
                    <label for="nombre_buscar">Nombre del Cliente</label>
                    <input type="text" id="nombre_buscar" name="nombre_buscar" required>
                </div>
                <button type="submit" name="buscar_cliente" class="btn">Buscar Cliente</button>
            </form>

            <?php if ($client_data): ?>
                <h4>Cliente Encontrado</h4>
                <p><strong>Nombre:</strong> <?php echo $client_data['nombre']; ?></p>
                <p><strong>NIT:</strong> <?php echo $client_data['nit']; ?></p>
                <p><strong>Dirección:</strong> <?php echo $client_data['direccion']; ?></p>
                <p><strong>Teléfono:</strong> <?php echo $client_data['telefono']; ?></p>

                <form action="crear_pedido.php" method="POST">
                    <input type="hidden" name="id_cliente" value="<?php echo $client_data['id_cliente']; ?>">
                    <div class="input-field">
                        <label for="fecha_entrega">Fecha de Entrega</label>
                        <input type="date" id="fecha_entrega" name="fecha_entrega" required>
                    </div>

                    <h4>Productos Disponibles</h4>
                    <?php while ($producto = $productos_result->fetch_assoc()): ?>
                        <div class="input-field">
                            <label for="producto_<?php echo $producto['id_producto']; ?>"><?php echo $producto['nombre']; ?> - $<?php echo number_format($producto['precio_unitario'], 2); ?></label>
                            <input type="number" id="producto_<?php echo $producto['id_producto']; ?>" name="producto[]" value="<?php echo $producto['id_producto']; ?>" hidden>
                            <input type="number" name="cantidad[]" placeholder="Cantidad" min="0">
                        </div>
                    <?php endwhile; ?>

                    <button type="submit" name="crear_pedido" class="btn">Crear Pedido</button>
                </form>
            <?php endif; ?>
        </div>
        <button onclick="window.location.href='pedidos.php'" class="btn">Regresar</button>

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
/* Estilo para el botón de regresar */
.btn-regresar {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007BFF;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 20px;
    text-align: center;
}

.btn-regresar:hover {
    background-color: #0056b3;
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