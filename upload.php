<?php
session_start();
require 'db.php';

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$sql = "SELECT id, last_posted_at FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id, $last_posted_at);
$stmt->fetch();
$stmt->close();

// Obsługa przesyłania zdjęcia
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title']; // Pobranie tytułu z formularza
    $description = $_POST['description'];

    // Walidacja długości tytułu i opisu
    if (strlen($title) > 40) {
        $error = "Tytuł nie może mieć więcej niż 40 znaków.";
    } elseif (strlen($description) > 500) {
        $error = "Opis nie może mieć więcej niż 500 znaków.";
    } else {
        // Sprawdź cooldown
        $current_time = new DateTime();
        $last_post_time = $last_posted_at ? new DateTime($last_posted_at) : new DateTime('1970-01-01');
        $interval = $current_time->diff($last_post_time);

        if ($interval->h < 12 && $interval->days == 0) {
            $error = "Możesz dodać nowe zdjęcie tylko co 12 godzin.";
        } else {
            // Obsługa pliku
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Sprawdź, czy plik jest obrazem
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $error = "Plik nie jest obrazem.";
                $uploadOk = 0;
            }

            // Sprawdź, czy plik już istnieje
            if (file_exists($target_file)) {
                $error = "Plik o tej nazwie już istnieje.";
                $uploadOk = 0;
            }

            // Sprawdź rozmiar pliku
            if ($_FILES["image"]["size"] > 5000000) { // 1MB limit
                $error = "Plik jest zbyt duży.";
                $uploadOk = 0;
            }

            // Dopuszczalne formaty plików
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                $error = "Dozwolone są tylko pliki JPG, JPEG, PNG.";
                $uploadOk = 0;
            }

            if ($uploadOk) {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $sql = "INSERT INTO posts (user_id, title, image_url, description, created_at) VALUES (?, ?, ?, ?, NOW())";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isss", $user_id, $title, $target_file, $description);
                    $stmt->execute();
                    $stmt->close();

                    // Zaktualizuj czas ostatniego przesłania zdjęcia
                    $sql = "UPDATE users SET last_posted_at = NOW() WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $stmt->close();

                    // Przekieruj użytkownika na stronę główną
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Wystąpił błąd podczas przesyłania pliku.";
                }
            }
        }
    }
}
?>

<link rel="stylesheet" href="style.css">
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj zdjęcie - Instagram Clone</title>
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
                    <h1 class="display-4 text-center">Dodawanie postu</h1>
    <hr>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Tytuł zdjęcia</label>
            <input type="text" class="form-control" id="title" name="title" maxlength="40" required>
        </div>
        <div class="form-group">
            <label for="description">Opis zdjęcia</label>
            <textarea class="form-control" id="description" name="description" rows="3" maxlength="500" required></textarea>
        </div>
        <div class="form-group">
            <label for="image">Wybierz zdjęcie</label>
            <input type="file" class="form-control-file" id="image" name="image" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Prześlij zdjęcie</button>
    </form>
</div>    <!-- Prawy banner -->
                <div class="col-md-2">
                    <div class="card mb-4">
                        <img class="card-img-top" src="ss2.png" alt="Right Banner">
 
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
