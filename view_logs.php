<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany jako administrator
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    // Jeżeli użytkownik nie jest zalogowany, przekieruj go na stronę logowania administratora
    header("Location: admin_login.php");
    exit();
}

$log_file = "login_attempts.log";

// Sprawdź, czy formularz do czyszczenia logów został przesłany
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clear_logs'])) {
    // Wyczyść plik logów
    file_put_contents($log_file, '');
    header("Location: view_logs.php"); // Odśwież stronę, aby zaktualizować listę logów
    exit();
}

// Pobierz zawartość pliku logów
$log_entries = file_exists($log_file) ? file($log_file) : [];
?><!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>Logi</title>
</head><?php include 'navbar.php'; ?>
<body>
<div class="container-fluid">
    <div class="jumbotron">
        <h1 class="display-4">Logi Logowania</h1>
        <p class="lead">Poniżej znajdują się logi prób logowania do panelu administratora.</p>
        <hr class="my-4">
        <a href="admin_panel.php" class="btn btn-primary">Wróć do Panelu Administratora</a>
        <a href="logout.php" class="btn btn-danger">Wyloguj</a>
        <form method="post" class="mt-3">
            <button type="submit" name="clear_logs" class="btn btn-warning">Wyczyść logi</button>
        </form>
        <div class="mt-4">
            <?php if (!empty($log_entries)): ?>
                <ul class="list-group">
                    <?php foreach ($log_entries as $entry): ?>
                        <li class="list-group-item"><?php echo htmlspecialchars($entry); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Brak logów do wyświetlenia.</p>
            <?php endif; ?>
        </div>
    </div>   <?php include 'footer.php'; ?>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
