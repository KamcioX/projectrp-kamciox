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

?>
<!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">
<doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>Regulamin - KamcioX ProjectRP</title>
    <script>
function highlightWord() {
    var searchText = document.getElementById("search-word").value.trim();
    
    // Sprawdź, czy pole wyszukiwania nie jest puste
    if (searchText === "") {
        alert("Pole wyszukiwania nie może być puste!");
        return;
    }
    
    var contentElement = document.getElementById("regulations");
    var content = contentElement.innerHTML;

    // Usuń istniejące podświetlenie
    var highlightedContent = content.replace(/<span style="background-color: yellow;">(.*?)<\/span>/g, '$1');

    // Use a temporary div to safely parse and manipulate the HTML
    var tempDiv = document.createElement("div");
    tempDiv.innerHTML = highlightedContent;

    // Function to recursively highlight text nodes
    function highlightNodes(node) {
        if (node.nodeType === 3) { // Text node
            var regex = new RegExp(searchText, 'gi');
            var parent = node.parentNode;
            var newHTML = node.nodeValue.replace(regex, match => `<span style="background-color: yellow;">${match}</span>`);
            var newSpan = document.createElement("span");
            newSpan.innerHTML = newHTML;
            parent.replaceChild(newSpan, node);
        } else if (node.nodeType === 1 && node.nodeName !== "SCRIPT" && node.nodeName !== "STYLE") { // Element node, not script or style
            for (var i = 0; i < node.childNodes.length; i++) {
                highlightNodes(node.childNodes[i]);
            }
        }
    }

    // Start the highlighting process
    highlightNodes(tempDiv);

    // Replace the original content with the highlighted content
    contentElement.innerHTML = tempDiv.innerHTML;
}
</script>
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
        <h1 class="display-4">Regulamin</h1>
        <p class="lead">Łatwiejsze wyszukiwanie słówek w regulaminie :)</p>
        <hr class="my-4"> <div class="d-flex justify-content-center">
        <form class="form-inline mb-2">
            <input class="form-control mr-sm-2" type="search" placeholder="Wyszukaj słowo" aria-label="Search" id="search-word">
            <button class="btn btn-primary" type="button" onclick="highlightWord()">Wyszukaj</button>
        </form></div>
        <p id="regulations">
            <!-- Tutaj wklej cały tekst regulaminu -->
            <b>§1. Postanowienia ogólne</b>
            <br>0.0 Zakazuje się kopiowania owego regulaminu. Jest on własnością serwera KamcioX ProjectRP.pl
            <br>1.0 Administracja nie ma obowiązku odpisywać na wiadomości prywatne. Do kontaktu z nią macie tickety.
            <br>1.1 Łącząc się z naszym serwerem automatycznie akceptujesz regulamin.
            <br>1.1.1 Nieznajomość regulaminu nie zwalnia Cię z jego przestrzegania.
            <br>1.1.2 Złamanie regulaminu pod wpływem alkoholu lub innej używki nie zwalnia z odpowiedzialności.
            <br>1.1.3 Administracja ma prawo do zablokowania osoby będącej pod wpływem różnych używek.
            <br>1.2 Zakazuje się wszelkich prób sprzedaży wirtualnego majątku poza grą. Administracja nie ponosi odpowiedzialności za straty materialne wskutek takich działań i ma prawo do permamentnego zablokowania takiego gracza.
            <br>1.3 Zarząd serwera ma prawo karać graczy za czyny, które nie są uwzględnione w regulaminie.
            <br>1.4 Zarząd Serwera ma prawo do zmiany liderów frakcji bez podania przyczyny.
            <br>1.5 Gracz ma 24h na zgłoszenie przewinienia na kanale pomocy.
            <br>1.6 Za bany 24h nie ma możliwości odwołania chyba, że został nadany niesłusznie.
            <br>1.7 Zakazuje się łamania regulaminu Twitcha.

          

</p> <form method="post">
        <a href="main.php" class="btn btn-primary">Strona Główna</a>   <button type="submit" name="logout" class="btn btn-danger">Wyloguj się</button>
             </form>
    </div> <!-- Prawy banner -->
     <div class="col-md-2">
                <div class="card mb-4">
                    <img class="card-img-top" src="ss2.png" alt="Right Banner">
                </div>
            </div>
        </div>
    </div>
</div>   
    </div>
    </div>
    
    <?php include 'footer.php'; ?>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first --->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>