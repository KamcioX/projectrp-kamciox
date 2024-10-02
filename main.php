<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$allowed_roles = ['Support', 'Moderator', 'Administrator', 'Management'];

if (!in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$show_login_message = false;
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === false) {
    $_SESSION['login_success'] = false;
    $show_login_message = true;
}
?>
<!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
        integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>Strona Główna - KamcioX ProjectRP</title>
</head>

<body class="custom-bg-color">

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

            <div class="col-md-8">
                <h1 class="display-4 text-center">Administracja</h1>
                <p class="lead text-center">Pare fajnych ciekawych rzeczy dla administratora.</p>
                <hr class="my-4">
                
                <div class="row">
                    <div class="col text-center">
                        <!-- Pierwszy zestaw kart -->
                        <div class="card-deck">
                            <div class="card">
                                <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="card-title">Norma tygodniowa</h5>
                                    <p class="card-text">Zobacz do kiedy obowiązuje aktualna norma tygodniowa.</p>
                                    <p class="card-text"><a href="view_weekly_norm.php" type="button" class="btn btn-secondary btn-sm">Norma tygodniowa</a></p>
                                </div>
                            </div>
                            <div class="card">
                                <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="card-title">Nakaz nagrywania</h5>
                                    <p class="card-text">Tutaj możesz wypisać osoby, które ostatnio sprawdziłeś z rangą nakaz nagrywania.</p>
                                    <p class="card-text"><a href="nakaz_nagrywania.php" type="button" class="btn btn-secondary btn-sm">Nakaz nagrywania</a></p>
                                </div>
                            </div>
                            <div class="card">
                                <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="card-title">Respname</h5>
                                    <p class="card-text">Nazwa mówi sama za siebie, sprawdź respname danego przedmiotu.</p>
                                    <p class="card-text"><a href="respname.php" type="button" class="btn btn-secondary btn-sm">Respname</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dodaj drugi zestaw kart tutaj -->
                <div class="row mt-4">
                    <div class="col text-center">
                        <div class="card-deck">
                            <div class="card">
                                <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="card-title">Taryfikator</h5>
                                    <p class="card-text">Zobacz aktualny taryfikator na serwerze, na jaki czas możesz kogoś zbanować za dane przewinienie.</p>
                                    <p class="card-text"><a href="taryfikator.php" type="button" class="btn btn-secondary btn-sm">Taryfikator</a></p>
                                </div>
                            </div>
                            <div class="card">
                                <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="card-title">Regulamin</h5>
                                    <p class="card-text">Regulamin KamcioX ProjectRP, możesz szybciej wyszukać jakieś słówko!</p>
                                    <p class="card-text"><a href="regulamin.php" type="button" class="btn btn-secondary btn-sm">Regulamin</a></p>
                                </div>
                            </div>
                            <div class="card">
                                <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="card-title">Komendy</h5>
                                    <p class="card-text">Aktualne komendy na serwerze dla administratora!</p>
                                    <p class="card-text"><a href="komendy.php" type="button" class="btn btn-secondary btn-sm">Komendy</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dodaj trzeci zestaw kart tutaj -->
                <div class="row mt-4">
                    <div class="col text-center">
                        <div class="card-deck">
                            <div class="card">
                                <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                                <div class="card-body">
                                    <h5 class="card-title">Egzamin</h5>
                                    <p class="card-text">Sprawdź swoją obecną wiedzę jako administrator! ;)</p>
                                    <p class="card-text"><a href="quiz.php" type="button" class="btn btn-secondary btn-sm">Egzamin</a></p>
                                </div>
                            </div>
                  
                        </div>
                    </div>
                </div>
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

    <?php include 'footer.php'; ?>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

</body>

</html>
