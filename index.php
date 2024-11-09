<?php
session_start();
include 'conexion.php';

// Generación de números aleatorios para el captcha de suma solo si aún no existe un captcha
if (!isset($_SESSION['captcha_num1']) && !isset($_SESSION['captcha_num2'])) {
    $_SESSION['captcha_num1'] = rand(1, 10);
    $_SESSION['captcha_num2'] = rand(1, 10);
}

// Captcha resultado esperado
$captcha_result = $_SESSION['captcha_num1'] + $_SESSION['captcha_num2'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $captcha = $_POST['captcha'];

    // Verificación del captcha ingresado
    if ($captcha == $captcha_result) {
        // Consultar en la base de datos
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE nombre = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['contraseña'])) {
                $_SESSION['usuario'] = $username;

                // Reinicia el captcha para la siguiente sesión
                unset($_SESSION['captcha_num1'], $_SESSION['captcha_num2']);
                header("Location: menu.php");
                exit();
            } else {
                $error_message = "Contraseña incorrecta.";
            }
        } else {
            $error_message = "Usuario no encontrado.";
        }
    } else {
        $error_message = "Captcha incorrecto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - ROA-Order</title>
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
        input[type="text"], input[type="password"], input[type="number"] {
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
        }
        .btn:hover {
            background-color: #455a64;
        }
        .error-message {
            color: #e57373;
            font-size: 14px;
        }
    </style>
</head>


<body>


    <div class="container">
        <h3>Iniciar Sesión</h3>
        <div class="card">
            <form action="index.php" method="POST">
                <div class="input-field">
                    <label for="username">Nombre de usuario</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-field">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-field">
                    <!-- Mostrar la suma de verificación -->
                    <label for="captcha">
                        <?php echo $_SESSION['captcha_num1'] . " + " . $_SESSION['captcha_num2'] . " = ?"; ?>
                    </label>
                    <input type="number" id="captcha" name="captcha" required>
                </div>
                <?php if (isset($error_message)) : ?>
                    <p class="error-message"><?php echo $error_message; ?></p>
                <?php endif; ?>
                <button type="submit" class="btn">Ingresar</button>
            </form>
        </div>
    </div>
    
</body>
</html>
