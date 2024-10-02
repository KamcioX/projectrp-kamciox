<?php
session_start();

// Sprawdź, czy użytkownik jest zalogowany jako administrator
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    // Jeżeli użytkownik nie jest zalogowany, przekieruj go na stronę logowania administratora
    header("Location: admin_login.php");
    exit();
}

// Inicjalizuj zmienne
$month = "";
$username = "";
$avatar = "";
$success_message = "";

// Sprawdź, czy formularz został przesłany
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pobierz dane z formularza
    $month = $_POST["month"];
    $username = $_POST["username"];
    $avatar = $_POST["avatar"];
    
    // Tutaj możesz zapisać dane do bazy danych lub przetworzyć je w inny sposób
    // Np. zapisz top 5 użytkowników do pliku top_data.txt
    // Użyj separatora | do oddzielenia poszczególnych danych użytkownika
    $data = "$month|$username|$avatar\n";
    if(file_put_contents("top_data.txt", $data) !== false) {
        // Zapisano dane pomyślnie
        $success_message = "Pomyślnie ustawiono top gracza!";
    } else {
        // Wystąpił błąd podczas zapisywania danych
        $success_message = "Wystąpił błąd podczas ustawiania top gracza!";
    }
}
?>
<!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ustaw Top</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head><?php include 'navbar.php'; ?>
<body>
    <div class="container-fluid">
        <div class="jumbotron">
            <h1 class="display-4">Ustaw Administratora Miesiąca</h1>
            <p class="lead">Użyj poniższego formularza, aby ustawić top użytkownika na stronie Administrator Miesiąca.</p>
        
            <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="month">Miesiąc:</label>
                <input type="text" class="form-control" id="month" name="month" value="<?php echo $month; ?>">
            </div>
            <div class="form-group">
                <label for="username">Nazwa użytkownika:</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>">
            </div>
            <div class="form-group">
                <label for="avatar">Awatar użytkownika (URL):</label>
                <input type="text" class="form-control" id="avatar" name="avatar" value="<?php echo $avatar; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Zapisz</button> <a href="admin_panel.php" class="btn btn-secondary">Wróć do Panelu</a>
        </form>
    </div></div> <?php include 'footer.php'; ?>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
