<?php
session_start();
require 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION["logged_in"] = true;
            $_SESSION["username"] = $user['username'];
            $_SESSION["role"] = $user['role'];

            // Debugging output
            error_log("User role: " . $user['role']);

            if ($user['role'] === 'użytkownik') {
                header("Location: profile.php");
                exit();
            } else {
                header("Location: profile.php");
                exit();
            }
        } else {
            $error = "Niepoprawne hasło. Spróbuj ponownie.";
        }
    } else {
        $error = "Nie znaleziono użytkownika o podanym nicku.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags and Bootstrap CSS -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <title>Logowanie - KamcioX ProjectRP</title>
</head>
<!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">
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
                <h1 class="display-4">Panel logowania</h1>
                <p class="lead">Zaloguj się do swojego konta</p>
                <hr class="my-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="username">Nick</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Hasło</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Zaloguj się</button>
                    <a href="register.php" class="btn btn-info">Nie posiadasz konta? Zarejestruj się!</a>
                </form>
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

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3MiykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>    <?php include 'footer.php'; ?>
