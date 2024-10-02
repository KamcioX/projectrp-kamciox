<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany jako administrator
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    // Jeżeli użytkownik nie jest zalogowany, przekieruj go na stronę logowania administratora
    header("Location: admin_login.php");
    exit();
}
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

    <title>Admin Panel - KamcioX ProjectRP</title>
</head><?php include 'navbar.php'; ?>
<body>
<div class="container-fluid">
    <div class="jumbotron">
        <h1 class="display-4">Admin Panel</h1>
        <p class="lead">Witaj w panelu administratora. Tutaj możesz zarządzać stroną.</p>
        <hr class="my-4">
        <a href="norma.php" class="btn btn-primary">Sprawdzanie Normy Tygodniowej</a>
        <a href="trial_support.php" class="btn btn-primary">Trial Support</a>
        <a href="ustaw_top.php" class="btn btn-primary">Ustaw Top</a>
        <a href="view_logs.php" class="btn btn-primary">Logi</a>
        <a href="tytuly.php" class="btn btn-primary">Tytuły</a>
        <a href="set_weekly_norm.php" class="btn btn-primary">Norma tygodniowa</a>
        <a href="logout.php" class="btn btn-danger">Wyloguj</a>
    </div>   <?php include 'footer.php'; ?>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
