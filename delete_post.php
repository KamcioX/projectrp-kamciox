<?php
session_start();
require 'db.php';

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];

    // Pobierz ID użytkownika na podstawie nazwy użytkownika z sesji
    $username = $_SESSION['username'];
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    // Sprawdź, czy post należy do zalogowanego użytkownika
    $sql = "SELECT image_url FROM posts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($image_url);
    if ($stmt->fetch()) {
        $stmt->close();

        // Usuń post z bazy danych
        $sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Usuń plik obrazu z serwera
        if (file_exists($image_url)) {
            unlink($image_url);
        }

        // Przekieruj na stronę główną
        header("Location: instagram.php");
        exit();
    } else {
        $stmt->close();
        echo "Nie masz uprawnień do usunięcia tego posta.";
    }
} else {
    header("Location: instagram.php");
    exit();
}
?>
