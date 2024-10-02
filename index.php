<?php
session_start();
?><style>.carousel-item img {
    width: 100%;
    height: 400px;
    object-fit: cover;
}</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Strona Główna - KamcioX ProjectRP</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- Custom CSS for Dark Mode (if needed) -->
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
                    <h1 class="display-4 text-center">Strona Główna</h1>
                    <p class="lead text-center">KamcioX ProjectRP - Najlepszy serwer WL-OFF w Polsce</p>
                    <hr class="my-4">
                    <center><img src="logo.png" alt="Left Banner" width="30%" height="20%"></center>
                    <div class="row">
        <div class="col text-center">
            <!-- Pierwszy zestaw kart -->
            <div class="card-deck">
                <div class="card">
                    <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">Połącz z serwerem</h5>
                        <p class="card-text">Już teraz zacznij rozgrywkę na Naszym serwerze! Czekamy na Ciebie!</p>
                        <p class="card-text"><a href="#" type="button" class="btn btn-secondary btn-sm">Połącz z serwerem</a></p>
                    </div>
                </div>
                <div class="card">
                    <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">Sklep</h5>
                        <p class="card-text">Chciałbyś kupić coś na serwerze? Pojazd limitowany? Mieszkanie? U Nas jest to możliwe!</p>
                        <p class="card-text"><a href="#" type="button" class="btn btn-secondary btn-sm">Sklep</a></p>
                    </div>
                </div>
                <div class="card">
                    <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">Discord</h5>
                        <p class="card-text">Dołącz na discorda serwera już teraz, aby móc się połączyć z serwerem!</p>
                        <p class="card-text"><a href="#" type="button" class="btn btn-secondary btn-sm">Discord</a></p>
                    </div>
                </div>
            </div>
            
            <!-- Drugi zestaw kart -->
            <div class="card-deck mt-4">
                <div class="card">
                    <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">Zaloguj sie na strone</h5>
                        <p class="card-text">Zaloguj sie na strone KamcioX ProjectRP!</p>
                        <p class="card-text"><a href="login.php" type="button" class="btn btn-secondary btn-sm">Zaloguj sie</a></p>
                    </div>
                </div>
                <div class="card">
                    <img class="card-img-top" src="banner.jpg" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">Zarejestruj konto</h5>
                        <p class="card-text">Musisz założyć konto, aby móc w pełni korzystać ze strony KamcioX ProjectRP!</p>
                        <p class="card-text"><a href="register.php" type="button" class="btn btn-secondary btn-sm">Zarejestruj sie</a></p>
                    </div>
                </div>
            </div>

            <br><hr><div id="demo" class="carousel slide" data-ride="carousel">

<!-- Indicators -->
<ul class="carousel-indicators">
  <li data-target="#demo" data-slide-to="0" class="active"></li>
  <li data-target="#demo" data-slide-to="1"></li>
  <li data-target="#demo" data-slide-to="2"></li>
</ul>

<!-- The slideshow -->
<div class="carousel-inner">
  <div class="carousel-item active">
  <center> <img src="banner2.png" alt="banner" class="d-block w-50"></center>
  </div>
  <div class="carousel-item">
  <center> <img src="banner3.png" alt="banner" class="d-block w-50"></center>
  </div>
  <div class="carousel-item">
   <center> <img src="banner4.png" alt="banner" class="d-block w-50"></center>
  </div>
</div>

<!-- Left and right controls -->
<a class="carousel-control-prev" href="#demo" data-slide="prev">
  <span class="carousel-control-prev-icon"></span>
</a>
<a class="carousel-control-next" href="#demo" data-slide="next">
  <span class="carousel-control-next-icon"></span>
</a>

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
    
    <!-- Script to copy IP to clipboard -->
    <script>
        function copyIP() {
            var tempInput = document.createElement("input");
            tempInput.value = "123.45.67.89"; // Replace with your server IP
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            alert("IP serwera zostało skopiowane!");
        }
    </script>
</body>
</html>