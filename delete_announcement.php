<?php
session_start();
require 'db.php';

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        $announcement_id = (int)$_POST['id'];  // Używamy 'id' zamiast 'announcement_id'

        // Pobierz ID użytkownika na podstawie nazwy użytkownika z sesji
        $username = $_SESSION['username'];
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        // Debug: Sprawdź ID użytkownika
        // echo "User ID: " . $user_id . "<br>";

        // Sprawdź, czy ogłoszenie należy do zalogowanego użytkownika
        $sql = "SELECT user_id FROM announcements WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $announcement_id);
        $stmt->execute();
        $stmt->bind_result($owner_id);
        
        if ($stmt->fetch()) {
            $stmt->close();
            
            // Debug: Sprawdź ID właściciela ogłoszenia
            // echo "Announcement Owner ID: " . $owner_id . "<br>";

            if ($owner_id == $user_id) {
                // Usuń ogłoszenie z bazy danych
                $sql = "DELETE FROM announcements WHERE id = ? AND user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $announcement_id, $user_id);
                $stmt->execute();
                $stmt->close();

                // Przekieruj na stronę ogłoszeń
                header("Location: announcements.php");
                exit();
            } else {
                echo "Nie masz uprawnień do usunięcia tego ogłoszenia.";
            }
        } else {
            $stmt->close();
            echo "Nie znaleziono ogłoszenia z ID: " . $announcement_id;
        }
    } else {
        echo "Brak ID ogłoszenia w formularzu.";
    }
} else {
    header("Location: announcements.php");
    exit();
}
?>
