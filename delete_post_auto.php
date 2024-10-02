<?php
session_start();
require 'db.php';

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Sprawdź, czy POST_ID zostało przekazane
if (isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];

    // Usuń post z bazy danych
    $delete_stmt = $pdo->prepare("DELETE FROM postsgielda WHERE id = :id");
    $delete_stmt->execute(['id' => $post_id]);

    // Przekierowanie z komunikatem o sukcesie
    $_SESSION['message'] = "Post został usunięty.";
    header("Location: gielda.php");
    exit();
} else {
    // Jeśli nie podano POST_ID
    header("Location: gielda.php");
    exit();
}
?>