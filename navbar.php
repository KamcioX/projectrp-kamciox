<?php
require 'db.php'; // Zakładam, że masz plik do połączenia z bazą danych

// Włącz raportowanie błędów
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Funkcja do pobrania aktualnie zalogowanych użytkowników
function getLoggedInUsers($conn) {
    $sql = "SELECT username FROM users WHERE is_logged_in = 1"; // Zakładam, że masz kolumnę `is_logged_in` w tabeli `users`
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row['username'];
    }
    return $users;
}

$loggedInUsers = getLoggedInUsers($conn);
?>
<div id="loadingScreen">
    <div> <img src="logooff.png" alt="Loading Image" height="30">Wczytywanie strony<span id="loadingDots"></span></div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php"><img src="logooff.png" alt="KamcioX ProjectRP Logo" height="30">KamcioX ProjectRP</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
        <?php
        // Pobierz rolę użytkownika z sesji lub bazy danych
        $role = $_SESSION['role']; // Załóżmy, że rola jest przechowywana w sesji
        if (in_array($role, ['Support', 'Moderator', 'Administrator', 'Management'])):
        ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Administracja
                </a>
                <div class="dropdown-menu" aria-labelledby="adminDropdown">
                    <a class="dropdown-item" href="main.php">Strona Główna Administracji</a>
                    <a class="dropdown-item" href="view_weekly_norm.php">Norma Tygodniowa</a>
                    <a class="dropdown-item" href="nakaz_nagrywania.php">Nakaz Nagrywania</a>
                    <a class="dropdown-item" href="respname.php">Respname</a>
                    <a class="dropdown-item" href="taryfikator.php">Taryfikator</a>
                    <a class="dropdown-item" href="regulamin.php">Regulamin</a>
                    <a class="dropdown-item" href="komendy.php">Komendy</a>
                    <a class="dropdown-item" href="quiz.php">Egzamin</a>
                </div>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="https://tx.KamcioX ProjectRP.pl">TX Admin</a>
        </li>

        <?php endif; ?>

    <?php else: ?>
        <li class="nav-item">
            <a class="nav-link" href="login.php">Zaloguj się</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="register.php">Zarejestruj się</a>
        </li>
    <?php endif; ?>
    <li class="nav-item">
        <a class="nav-link" href="#">Połącz z serwerem</a>
    </li><?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
        <li class="nav-item">
            <a class="nav-link" href="instagram.php">Instagram</a>
        </li> 
        <li class="nav-item">
            <a class="nav-link" href="gielda.php">Giełda</a>
        </li> 
        <li class="nav-item">
            <a class="nav-link" href="ogloszenia.php">Ogłoszenia</a>
        </li>
        <li class="nav-item">
        <a class="nav-link" href="search.php">Wyszukaj profil</a>
    </li><?php endif; ?>
    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
        <li class="nav-item">
            <a class="nav-link" href="profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
        </li>
    <?php endif; ?>   <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">Wyloguj się</a>
        </li> 
    <?php endif; ?>
</ul>

        <div class="form-inline my-2 my-lg-0">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="darkModeSwitch">
                <label class="custom-control-label" for="darkModeSwitch">
                    <i class="fas fa-sun" id="sunIcon"></i>
                    <i class="fas fa-moon d-none" id="moonIcon"></i>
                </label>
            </div>
        </div>
    </div>
</nav>



<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
    // JavaScript do zarządzania trybem ciemnym
    document.addEventListener("DOMContentLoaded", function () {
        const darkModeSwitch = document.getElementById('darkModeSwitch');
        const sunIcon = document.getElementById('sunIcon');
        const moonIcon = document.getElementById('moonIcon');
        const darkModeClass = 'dark-mode';
        const localStorageKey = 'darkMode';

        function applyDarkMode() {
            const darkModeEnabled = localStorage.getItem(localStorageKey) === 'enabled';

            if (darkModeEnabled) {
                document.body.classList.add(darkModeClass);
                sunIcon.classList.add('d-none');
                moonIcon.classList.remove('d-none');
            } else {
                document.body.classList.remove(darkModeClass);
                sunIcon.classList.remove('d-none');
                moonIcon.classList.add('d-none');
            }

            darkModeSwitch.checked = darkModeEnabled;
        }

        darkModeSwitch.addEventListener('change', function () {
            localStorage.setItem(localStorageKey, this.checked ? 'enabled' : 'disabled');
            applyDarkMode();
        });

        // Apply dark mode on page load
        applyDarkMode();
    });
</script>

<script>
    // JavaScript to show loading dots animation
    document.addEventListener('DOMContentLoaded', function () {
        var dotsInterval = window.setInterval(function () {
            var loadingDots = document.getElementById('loadingDots');
            if (loadingDots.innerHTML.length > 2)
                loadingDots.innerHTML = "";
            else
                loadingDots.innerHTML += ".";
        }, 20);

        // Hide loading screen after 5 seconds with a fade out effect
        window.setTimeout(function () {
            clearInterval(dotsInterval); // Stop dots animation
            var loadingScreen = document.getElementById('loadingScreen');
            loadingScreen.style.opacity = '0'; // Start fade out
            setTimeout(function () {
                loadingScreen.style.display = 'none'; // Hide loading screen after fade out
            }, 500); // 0.5s delay after opacity transition
        }, 200); // 5s timeout
    });
</script>
