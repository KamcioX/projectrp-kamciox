<?php
session_start();
require 'db.php';

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

?>

<link rel="stylesheet" href="style.css">
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>Komendy - KamcioX ProjectRP</title>
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
        <h1 class="display-4">Komendy</h1>
        <p class="lead">Komendy, jakie możesz używać na serwerze</p>
        <hr class="my-4">
        <p><table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">Nazwa komendy</th>
      <th scope="col">Opis</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>/debil</td>
      <td>debil</td>
    </tr>
    <tr>
      <td>/setjob [id] [praca] [pozycja_joba]</td>
      <td>Nadawanie pracy</td>
    </tr>
   
  </tbody>
</table></p>
        <form method="post">
        <a href="main.php" class="btn btn-primary">Strona Główna</a>   <button type="submit" name="logout" class="btn btn-danger">Wyloguj się</button>
             </form>
    </div><!-- Prawy banner -->
     <div class="col-md-2">
                <div class="card mb-4">
                    <img class="card-img-top" src="ss2.png" alt="Right Banner">
                </div>
            </div>
        </div>
    </div>
</div>   
    <?php include 'footer.php'; ?>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
