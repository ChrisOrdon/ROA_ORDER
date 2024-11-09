<?php
// Conexión a la base de datos
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "roa_order_chatbot"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Función para detectar saludos
function checkGreetings($user_message) {
    $greetings = ['hola', 'buenos días', 'buenas tardes', 'buenas noches', 'hey'];
    foreach ($greetings as $greeting) {
        if (stripos($user_message, $greeting) !== false) {
            return true; // Se detectó un saludo
        }
    }
    return false; // No se detectó un saludo
}

// Función para obtener la respuesta de la base de datos
function getAnswer($user_message, $conn) {
    // Primero buscamos si la entrada tiene palabras clave relacionadas con la pregunta
    $stmt = $conn->prepare("SELECT * FROM faqs WHERE question LIKE ?");
    $search_query = "%" . $user_message . "%";  // Buscamos coincidencias parciales
    $stmt->bind_param("s", $search_query);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Si se encuentra alguna respuesta, mostramos la primera
        $row = $result->fetch_assoc();
        return $row['answer'];
    } else {
        // Si no se encuentra ninguna respuesta, mostramos una respuesta predeterminada
        return "Lo siento, no entendí eso. ¿Puedes ser más específico?";
    }
}

if (isset($_POST['user_message'])) {
    $user_message = strtolower(trim($_POST['user_message']));  // Convertimos el mensaje a minúsculas para evitar problemas de mayúsculas/minúsculas

    // Si se detecta un saludo, respondemos con un saludo
    if (checkGreetings($user_message)) {
        echo "¡Hola! ¿En qué puedo ayudarte?";
    } else {
        // Si no es un saludo, buscamos la respuesta basada en palabras clave
        echo getAnswer($user_message, $conn);
    }
}

// Cerrar la conexión
$conn->close();
?>
