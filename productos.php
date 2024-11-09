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

// Manejo del envío del formulario de agregar producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_producto'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $presentacion = $_POST['presentacion'];
    $costo_unitario = $_POST['costo_unitario'];
    $precio_unitario = $_POST['precio_unitario'];
    $tiempo_vida = $_POST['tiempo_vida'];

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, presentacion, costo_unitario, precio_unitario, tiempo_vida) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdds", $nombre, $descripcion, $presentacion, $costo_unitario, $precio_unitario, $tiempo_vida);

    if ($stmt->execute()) {
        $success_message = "Producto agregado exitosamente.";
    } else {
        $error_message = "Error al agregar producto: " . $stmt->error;
    }
    $stmt->close();
}

// Manejo de búsqueda de producto para edición
$product_data = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar_producto'])) {
    $nombre_buscar = $_POST['nombre_buscar'];
    $stmt = $conn->prepare("SELECT * FROM productos WHERE nombre = ?");
    $stmt->bind_param("s", $nombre_buscar);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product_data = $result->fetch_assoc();
    } else {
        $error_message = "Producto no encontrado.";
    }
    $stmt->close();
}

// Manejo del envío del formulario de edición de producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_producto'])) {
    $id_producto = $_POST['id_producto'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $presentacion = $_POST['presentacion'];
    $costo_unitario = $_POST['costo_unitario'];
    $precio_unitario = $_POST['precio_unitario'];
    $tiempo_vida = $_POST['tiempo_vida'];

    $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, presentacion = ?, costo_unitario = ?, precio_unitario = ?, tiempo_vida = ? WHERE id_producto = ?");
    $stmt->bind_param("sssddsi", $nombre, $descripcion, $presentacion, $costo_unitario, $precio_unitario, $tiempo_vida, $id_producto);

    if ($stmt->execute()) {
        $success_message = "Producto actualizado exitosamente.";
    } else {
        $error_message = "Error al actualizar el producto: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos - ROA-Order</title>
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
        <h3>Gestión de Productos</h3>

        <div class="action-buttons">
            <button onclick="showSection('agregar')" class="btn">Agregar Producto</button>
            <button onclick="showSection('editar')" class="btn">Editar Producto</button>
            <button onclick="window.location.href='menu.php'" class="btn">Regresar</button>
        </div>

        <!-- Sección para agregar producto -->
        <div id="agregar" class="card" style="display: none;">
            <h4>Agregar Producto</h4>
            <form action="productos.php" method="POST">
                <div class="input-field">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="input-field">
                    <label for="descripcion">Descripción</label>
                    <input type="text" id="descripcion" name="descripcion" required>
                </div>
                <div class="input-field">
                    <label for="presentacion">Presentación</label>
                    <input type="text" id="presentacion" name="presentacion" required>
                </div>
                <div class="input-field">
                    <label for="costo_unitario">Costo Unitario</label>
                    <input type="number" id="costo_unitario" name="costo_unitario" step="0.01" required>
                </div>
                <div class="input-field">
                    <label for="precio_unitario">Precio Unitario</label>
                    <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" required>
                </div>
                <div class="input-field">
                    <label for="tiempo_vida">Tiempo de Vida (días)</label>
                    <input type="number" id="tiempo_vida" name="tiempo_vida" required>
                </div>
                <button type="submit" name="agregar_producto" class="btn">Agregar Producto</button>
            </form>
        </div>

        <!-- Sección para buscar y editar producto -->
        <div id="editar" class="card" style="display: none;">
            <h4>Editar Producto</h4>
            <form action="productos.php" method="POST">
                <div class="input-field">
                    <label for="nombre_buscar">Nombre del Producto</label>
                    <input type="text" id="nombre_buscar" name="nombre_buscar" required>
                </div>
                <button type="submit" name="buscar_producto" class="btn">Buscar Producto</button>
            </form>

            <?php if ($product_data): ?>
            <form action="productos.php" method="POST">
                <input type="hidden" name="id_producto" value="<?php echo $product_data['id_producto']; ?>">
                <div class="input-field">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo $product_data['nombre']; ?>" required>
                </div>
                <div class="input-field">
                    <label for="descripcion">Descripción</label>
                    <input type="text" id="descripcion" name="descripcion" value="<?php echo $product_data['descripcion']; ?>" required>
                </div>
                <div class="input-field">
                    <label for="presentacion">Presentación</label>
                    <input type="text" id="presentacion" name="presentacion" value="<?php echo $product_data['presentacion']; ?>" required>
                </div>
                <div class="input-field">
                    <label for="costo_unitario">Costo Unitario</label>
                    <input type="number" id="costo_unitario" name="costo_unitario" step="0.01" value="<?php echo $product_data['costo_unitario']; ?>" required>
                </div>
                <div class="input-field">
                    <label for="precio_unitario">Precio Unitario</label>
                    <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" value="<?php echo $product_data['precio_unitario']; ?>" required>
                </div>
                <div class="input-field">
                    <label for="tiempo_vida">Tiempo de Vida (días)</label>
                    <input type="number" id="tiempo_vida" name="tiempo_vida" value="<?php echo $product_data['tiempo_vida']; ?>" required>
                </div>
                <button type="submit" name="editar_producto" class="btn">Actualizar Producto</button>
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
