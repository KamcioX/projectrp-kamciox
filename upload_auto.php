<?php
session_start();
require 'db.php';

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sprawdzenie, czy sesja zawiera zmienną 'username'
    if (!isset($_SESSION['username'])) {
        echo "Zmienna sesji 'username' nie jest ustawiona.";
        exit;
    }

    // Pobranie user_id na podstawie username
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$_SESSION['username']]);
    $user_id = $stmt->fetchColumn();

    if (!$user_id) {
        echo "Nie znaleziono user_id dla użytkownika.";
        exit;
    }

    $current_time = time(); // Czas obecny
    $cooldown_period = 6 * 60 * 60; // Cooldown 6 godzin (w sekundach)

    // Pobranie ostatniego czasu dodania posta z bazy danych
    $stmt = $pdo->prepare('SELECT last_post_time FROM users WHERE username = ?');
    $stmt->execute([$_SESSION['username']]);
    $last_post_time = $stmt->fetchColumn();

    if ($last_post_time) {
        $last_post_time_timestamp = strtotime($last_post_time); // Konwersja do UNIX timestamp

        if (($current_time - $last_post_time_timestamp) < $cooldown_period) {
            $remaining_time = $cooldown_period - ($current_time - $last_post_time_timestamp);
            $hours = floor($remaining_time / 3600);
            $minutes = floor(($remaining_time % 3600) / 60);
            $seconds = $remaining_time % 60;

            $_SESSION['cooldown_message'] = "Kolejny post możesz dodać za $hours godzin $minutes minut $seconds sekund.";
            header("Location: upload_auto.php"); // Przekierowanie, aby odświeżyć stronę i wyświetlić komunikat
            exit;
        }
    }

    $title = $_POST['title'];
    $price = $_POST['price'];
    $is_negotiable = $_POST['is_negotiable'];
    $full_tune = $_POST['full_tune'];
    $full_visual = $_POST['full_visual'];
    $description = $_POST['description'];
    $phone = $_POST['phone'];
    $image_path = '';

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    // Dodanie posta do bazy danych z user_id
    $stmt = $pdo->prepare('INSERT INTO postsgielda (type, title, description, price, is_negotiable, full_tune, full_visual, phone, image_path, user_id) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute(['auto', $title, $description, $price, $is_negotiable, $full_tune, $full_visual, $phone, $image_path, $user_id]);

    // Aktualizacja czasu ostatniego posta
    $stmt = $pdo->prepare('UPDATE users SET last_post_time = NOW() WHERE username = ?');
    $stmt->execute([$_SESSION['username']]);

    $_SESSION['form_submitted'] = true;

    // Przekierowanie na stronę 'gielda.php'
    header("Location: gielda.php");
    exit();
}
?>


<link rel="stylesheet" href="style.css">
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wystaw auto na sprzedaż - KamcioX ProjectRP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
    </style>
</head>
<body><?php include 'navbar.php'; ?>

<div class="container-fluid">
    <div class="jumbotron">
        <div class="row">
            <!-- Lewy banner -->
            <div class="col-md-2">
                <div class="card mb-4">
                    <img class="card-img-top" src="ss1.png" alt="Left Banner">
                </div>
            </div>
            
            <!-- Treść główna -->
            <div class="col-md-8">
                <h1 class="display-4 text-center">Wystaw auto na sprzedaż</h1>
                <p class="lead text-center"></p><hr><a href="gielda.php" class="btn btn-info mt-2 btn-lg btn-block">Wróć na giełde</a> 
                <hr class="my-4">
                
                <div class="row justify-content-center">
                    <?php
                    if (isset($_SESSION['cooldown_message'])) {
                        echo "<div class='alert alert-danger' role='alert'>
                                " . htmlspecialchars($_SESSION['cooldown_message']) . "
                              </div>";
                        unset($_SESSION['cooldown_message']); // Usuń komunikat po wyświetleniu
                    }
                    ?>

                    <form action="upload_auto.php" method="post" enctype="multipart/form-data" class="w-100">
                        <div class="form-group">
                            <label for="title">Nazwa Auta:</label>
                            <input type="text" name="title" class="form-control w-100" required>
                        </div>

                        <div class="form-group">
                            <label for="price">Cena:</label>
                            <input type="number" name="price" class="form-control w-100" required>
                        </div>

                        <div class="form-group">
                            <label for="is_negotiable">Możliwość Negocjacji:</label>
                            <select name="is_negotiable" class="form-control w-100">
                                <option value="yes">Tak</option>
                                <option value="no">Nie</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="full_tune">FullTune:</label>
                            <select name="full_tune" class="form-control w-100">
                                <option value="yes">Tak</option>
                                <option value="no">Nie</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="full_visual">Full Wizualka:</label>
                            <select name="full_visual" class="form-control w-100">
                                <option value="yes">Tak</option>
                                <option value="no">Nie</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Krótki opis:</label>
                            <textarea name="description" class="form-control w-100" maxlength="50" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="phone">Numer Telefonu IC:</label>
                            <input type="text" name="phone" class="form-control w-100" required>
                        </div>

                        <div class="form-group">
                            <label for="image">Prześlij Zdjęcie:</label>
                            <input type="file" name="image" class="form-control-file w-100">
                        </div>

                        <button type="submit" class="btn btn-secondary btn-block">Dodaj Auto</button>
                    </form>
                </div>
            </div>

            <!-- Prawy banner -->
            <div class="col-md-2">
                <div class="card mb-4">
                    <img class="card-img-top" src="ss2.png" alt="Right Banner">
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>