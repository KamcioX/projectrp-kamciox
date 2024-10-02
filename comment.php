<?php
session_start();
require 'db.php';

// Włącz raportowanie błędów
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Pobranie ID użytkownika na podstawie nazwy użytkownika z sesji
$username = $_SESSION['username'];
$sql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Obsługa przesyłania komentarza
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'], $_POST['comment'])) {
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];

    // Walidacja komentarza
    $comment = trim($comment);
    if (empty($comment)) {
        $error = "Komentarz nie może być pusty.";
    } else {
        // Zapisz komentarz do bazy danych z datą dodania
        $sql = "INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $post_id, $user_id, $comment);
        if ($stmt->execute()) {
            // Przekieruj użytkownika na stronę, z której przesłano formularz
            $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'instagram.php';
            header("Location: $redirect_url");
            exit();
        } else {
            $error = "Wystąpił błąd podczas dodawania komentarza.";
        }
        $stmt->close();
    }
}

// Wyświetlanie błędów
if (isset($error)) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
}
?>