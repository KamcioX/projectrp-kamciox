<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany jako administrator
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: admin_login.php");
    exit();
}

$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];

    // Zapisz dane do pliku
    $norm_data = [
        "start_date" => $start_date,
        "end_date" => $end_date,
        "start_time" => $start_time,
        "end_time" => $end_time
    ];

    file_put_contents("weekly_norm.json", json_encode($norm_data));
    $success_message = "Norma tygodniowa została ustawiona poprawnie.";

// Zapisz zmianę normy w logach
$log_file = "login_attempts.log"; // zmiana nazwy pliku
$current_time = date('Y-m-d H:i:s');
$log_message = $current_time . ' - Zmieniono normę tygodniową: Od ' . $start_date . ' ' . $start_time . ' do ' . $end_date . ' ' . $end_time . "\n";
file_put_contents($log_file, $log_message, FILE_APPEND);

}
?>
<!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Ustaw Norma Tygodniowa - KamcioX ProjectRP</title>
</head><?php include 'navbar.php'; ?>
<body>
<div class="container-fluid">
    <div class="jumbotron">
        <h1 class="display-4">Ustaw normę tygodniową</h1>
        <p class="lead">Wybierz daty i godziny dla normy tygodniowej.</p>
        <hr class="my-4">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="start_date">Data początkowa</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
            </div>
            <div class="form-group">
                <label for="end_date">Data końcowa</label>
                <input type="date" class="form-control" id="end_date" name="end_date" required>
            </div>
            <div class="form-group">
                <label for="start_time">Godzina początkowa</label>
                <input type="time" class="form-control" id="start_time" name="start_time" required>
            </div>
            <div class="form-group">
                <label for="end_time">Godzina końcowa</label>
                <input type="time" class="form-control" id="end_time" name="end_time" required>
            </div>
            <button type="submit" class="btn btn-primary">Ustaw</button>
            <a href="admin_panel.php" class="btn btn-secondary">Wróć do Panelu</a>
        </form>
    </div>
    <?php include 'footer.php'; ?>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
