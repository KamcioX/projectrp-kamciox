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

// Sprawdzenie, czy komunikat o zalogowaniu powinien być wyświetlony
$show_login_message = false;
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === false) {
    $_SESSION['login_success'] = false; // Ustaw na false, aby wyświetlić komunikat tylko raz
    $show_login_message = true;
}

$norm_file = "weekly_norm.json";
$norm_data = file_exists($norm_file) ? json_decode(file_get_contents($norm_file), true) : null;

function format_date($date) {
    $months = [
        '01' => 'stycznia',
        '02' => 'lutego',
        '03' => 'marca',
        '04' => 'kwietnia',
        '05' => 'maja',
        '06' => 'czerwca',
        '07' => 'lipca',
        '08' => 'sierpnia',
        '09' => 'września',
        '10' => 'października',
        '11' => 'listopada',
        '12' => 'grudnia'
    ];
    $date_parts = explode("-", $date);
    return intval($date_parts[2]) . " " . $months[$date_parts[1]] . " " . $date_parts[0];
}
?><!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Norma Tygodniowa - KamcioX ProjectRP</title>
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
        <h1 class="display-4">Norma Tygodniowa</h1>
        <p class="lead">Poniżej znajduje się ustawiona norma tygodniowa.</p>
        <hr class="my-4">
        <?php if ($norm_data): ?>
            <p>Norma tygodniowa trwa od <?php echo format_date(htmlspecialchars($norm_data["start_date"])); ?> godziny <?php echo htmlspecialchars($norm_data["start_time"]); ?> do <?php echo format_date(htmlspecialchars($norm_data["end_date"])); ?> godziny <?php echo htmlspecialchars($norm_data["end_time"]); ?>.</p>
        <?php else: ?>
            <p>Brak ustawionej normy tygodniowej.</p>
        <?php endif; ?>
        <a href="main.php" class="btn btn-primary">Strona Główna</a> <button type="submit" name="logout" class="btn btn-danger">Wyloguj się</button>
    </div>  <!-- Prawy banner -->
     <div class="col-md-2">
                <div class="card mb-4">
                    <img class="card-img-top" src="ss2.png" alt="Right Banner">
                </div>
            </div>
        </div>  </div>
        </div>  <?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
