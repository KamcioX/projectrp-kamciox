<?php
session_start();
require 'db.php';

$error = "";
$ip_address = $_SERVER['REMOTE_ADDR'];

// Sprawdź, czy rejestracja z tego adresu IP była w ciągu ostatniej godziny
$sql = "SELECT registration_timestamp FROM users WHERE registration_ip = ? ORDER BY registration_timestamp DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $ip_address);
$stmt->execute();
$stmt->bind_result($last_registration);
$stmt->fetch();
$stmt->close();

if ($last_registration) {
    $last_registration_time = strtotime($last_registration);
    $current_time = time();
    if (($current_time - $last_registration_time) < 3600) { // 3600 sekund = 1 godzina
        $error = "Możesz zarejestrować konto tylko raz na godzinę.";
    }
}

if (empty($error) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    
    // Check if the username already exists
    $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    
    if ($count > 0) {
        $error = "Konto z danym nickiem już istnieje.";
    } else {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $email = isset($_POST['email']) && !empty($_POST['email']) ? $conn->real_escape_string($_POST['email']) : NULL;
        $birthdate = isset($_POST['birthdate']) && !empty($_POST['birthdate']) ? $conn->real_escape_string($_POST['birthdate']) : NULL;

        // Prepare SQL query based on whether email and birthdate are provided
        if ($email && $birthdate) {
            $sql = "INSERT INTO users (username, password, email, birthdate, registration_ip, registration_timestamp) VALUES ('$username', '$password', '$email', '$birthdate', '$ip_address', NOW())";
        } elseif ($email) {
            $sql = "INSERT INTO users (username, password, email, registration_ip, registration_timestamp) VALUES ('$username', '$password', '$email', '$ip_address', NOW())";
        } elseif ($birthdate) {
            $sql = "INSERT INTO users (username, password, birthdate, registration_ip, registration_timestamp) VALUES ('$username', '$password', '$birthdate', '$ip_address', NOW())";
        } else {
            $sql = "INSERT INTO users (username, password, registration_ip, registration_timestamp) VALUES ('$username', '$password', '$ip_address', NOW())";
        }

        if ($conn->query($sql) === TRUE) {
            $_SESSION["logged_in"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["role"] = 'Uzytkownik';
            header("Location: profile.php");
            exit();
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!-- HTML for registration form -->
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags and Bootstrap CSS -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <title>Rejestracja - KamcioX ProjectRP</title>
    <!-- Custom CSS for Dark Mode -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
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
        <h1 class="display-4">Rejestracja</h1>
        <p class="lead">Zarejestruj swoje konto</p>
        <hr class="my-4">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        <form action="register.php" method="post">
    <div class="form-group">
        <label for="username">Nick</label>
        <input type="text" class="form-control" id="username" name="username" maxlength="30" required>
    </div>
    <div class="form-group">
        <label for="password">Hasło</label>
        <input type="password" class="form-control" id="password" name="password" maxlength="30" required>
    </div>
    <div class="form-group">
        <label for="email">Email (opcjonalnie)</label>
        <input type="email" class="form-control" id="email" name="email" maxlength="30">
    </div>
    <div class="form-group">
        <label for="birthdate">Data urodzenia (opcjonalnie)</label>
        <input type="date" class="form-control" id="birthdate" name="birthdate">
    </div>
    <button type="submit" class="btn btn-primary">Zarejestruj się</button>
</form>

    </div>   <!-- Prawy banner -->
            <div class="col-md-2">
                <div class="card mb-4">
                    <img class="card-img-top" src="ss2.png" alt="Right Banner">
                </div>
            </div>      </div>
    </div>
</div>
</div><script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3MiykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>    <?php include 'footer.php'; ?>
