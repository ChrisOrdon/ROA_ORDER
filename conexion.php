<?php
// Datos de conexión a la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'roa_order';

// Crear conexión
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
