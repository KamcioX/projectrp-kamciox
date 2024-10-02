<?php
// Ustawienia do połączenia z bazą danych
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "nazwabazydanych";

// Połączenie z MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzenie połączenia MySQLi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Połączenie z PDO
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    // Ustawienie trybu błędów PDO na wyjątki
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("PDO connection failed: " . $e->getMessage());
}
?>
