<?php
session_start();
require 'db.php';

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['username'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $username = $_SESSION['username'];

    // Pobierz user_id na podstawie username
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id === null) {
        echo json_encode(["error" => "User ID not found."]);
        exit();
    }

    // Sprawdź, czy użytkownik już polubił post
    $sql = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $liked = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if ($liked) {
        // Usuń polubienie
        $sql = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
        $stmt->close();
        $liked = false;
    } else {
        // Dodaj polubienie
        $sql = "INSERT INTO likes (post_id, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
        $stmt->close();
        $liked = true;
    }

    // Pobierz zaktualizowaną liczbę polubień oraz listę użytkowników
    $sql = "SELECT u.username FROM likes l JOIN users u ON l.user_id = u.id WHERE l.post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $like_count = $result->num_rows;
    $usernames = [];
    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row['username'];
    }
    $stmt->close();

    echo json_encode([
        "success" => true, 
        "liked" => $liked, 
        "like_count" => $like_count, 
        "usernames" => $usernames
    ]);
    exit();
} else {
    echo json_encode(["error" => "Invalid request."]);
    exit();
}
