<?php
session_start();

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Jeśli nie jest zalogowany, przekieruj do strony logowania
    header("Location: login.php");
    exit();
}

// Obsługa wylogowania
if (isset($_POST['logout'])) {
    // Zniszcz sesję i przekieruj na stronę logowania
    session_destroy();
    header("Location: login.php");
    exit();
}

$error = ""; // Zmienna przechowująca informacje o błędzie

// Sprawdź, czy formularz został przesłany
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_password = "admin123"; // Hasło administratora

    // Sprawdź, czy wprowadzone hasło jest poprawne
    if ($_POST["password"] == $admin_password) {
        // Przekieruj na stronę panelu administratora po poprawnym zalogowaniu
        $_SESSION["admin_logged_in"] = true;
        header("Location: admin_panel.php");
        exit();
    } else {
        // Zapisz próbę logowania do pliku logów
        $log_file = fopen("login_attempts.log", "a");
        $log_entry = "Nieudana próba logowania: " . date("Y-m-d H:i:s") . " - Hasło: " . $_POST["password"] . "\n";
        fwrite($log_file, $log_entry);
        fclose($log_file);

        $error = "Niepoprawne hasło. Spróbuj ponownie.";
    }
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

    <title>Admin Logowanie - KamcioX ProjectRP</title>
</head><?php include 'navbar.php'; ?>
<body>
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
        <h1 class="display-4">Admin Panel Logowania</h1>
        <p class="lead">Podaj swoje hasło, aby uzyskać dostęp do panelu administratora.</p>
        <hr class="my-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="password">Hasło</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Wpisz hasło...">
            </div>
            <button type="submit" class="btn btn-primary">Zaloguj</button>
            <a href="main.php" class="btn btn-primary">Strona Główna</a>
        </form>
    </div><!-- Prawy banner -->
     <div class="col-md-2">
                <div class="card mb-4">
                    <img class="card-img-top" src="ss2.png" alt="Right Banner">
                </div>
            </div>
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
