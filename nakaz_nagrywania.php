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


// Inicjalizuj zmienne
$discord_id = "";
$username = "";
$date = date("Y-m-d");
$success_message = "";
$error_message = "";

// Obsługa przycisku Zapisz
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["save"])) {
    // Pobierz dane z formularza
    $discord_id = $_POST["discord_id"];
    $username = $_POST["username"];
    $date = $_POST["date"];
    
    // Sprawdź, czy dane są unikalne
    $lines = file("nakaz_nagrywania.txt");
    $is_unique = true;
    foreach ($lines as $line) {
        $parts = explode("|", $line);
        if (count($parts) === 4 && $parts[1] == $discord_id && $parts[2] == $username && $parts[3] == $date) {
            $is_unique = false;
            break;
        }
    }
    
    // Zapisz dane do pliku, jeśli są unikalne
    if ($is_unique) {
        // Sprawdź, jakie ID są dostępne
        $available_ids = [];
        foreach ($lines as $line) {
            $parts = explode("|", $line);
            if (count($parts) === 4) {
                $available_ids[] = $parts[0];
            }
        }
        
        // Znajdź najmniejsze dostępne ID
        $next_id = 1;
        while (in_array($next_id, $available_ids)) {
            $next_id++;
        }
        
        // Zapisz dane do pliku
        $data = count($lines) > 0 ? "\n" : "";
        $data .= "$next_id|$discord_id|$username|$date";
        file_put_contents("nakaz_nagrywania.txt", $data, FILE_APPEND);
        $success_message = "Pomyślnie dodano nowy wpis.";
        
        // Przekieruj po zapisaniu danych, aby odświeżenie strony nie powodowało ponownego zapisu
        header("Location: nakaz_nagrywania.php");
        exit();
    } else {
        $error_message = "Wpis o podanych danych już istnieje.";
    }
}

// Obsługa przycisku Usuń
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    // Pobierz ID rekordu do usunięcia
    $id_to_delete = $_POST["id_to_delete"];
    
    // Usuń rekord z pliku
    $lines = file("nakaz_nagrywania.txt");
    $output = "";
    foreach ($lines as $line) {
        $parts = explode("|", $line);
        if (count($parts) === 4 && $parts[0] != $id_to_delete) {
            $output .= $line;
        }
    }
    file_put_contents("nakaz_nagrywania.txt", $output);
    $success_message = "Pomyślnie usunięto wpis.";
}

// Pobierz dane z pliku, jeśli istnieje
if (file_exists("nakaz_nagrywania.txt")) {
    $lines = file("nakaz_nagrywania.txt");
} else {
    $lines = [];
}
?>
<!-- Custom CSS for Dark Mode -->
    <link rel="stylesheet" href="style.css">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nakaz nagrywania - KamcioX ProjectRP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css"></head>
<body><?php include 'navbar.php'; ?>
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
            <h1 class="display-4">Nakaz nagrywania</h1>
            <p class="lead">Użyj poniższego formularza, aby dodać nowy wpis.</p>
            
            <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label for="discord_id">Discord ID:</label>
                    <input type="text" class="form-control" id="discord_id" name="discord_id" value="<?php echo $discord_id; ?>" pattern="\d{18}" title="Discord ID musi składać się dokładnie z 8 cyfr." required>
                    <small id="discord_id_help" class="form-text text-muted">Wprowadź dokładnie 18 cyfr</small>
                </div>
                <div class="form-group">
                    <label for="username">Nickname na discord:</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo $username; ?>" maxlength="30" required>
                    <small id="username_help" class="form-text text-muted">Wprowadź maksymalnie 30 liter</small>
                </div>
                <div class="form-group">
                    <label for="date">Data sprawdzenia nagrania:</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?php echo $date; ?>" required>
                </div>
                <button type="submit" name="save" class="btn btn-primary">Zapisz</button>  <a href="main.php" class="btn btn-primary">Strona Główna</a> <button type="submit" name="logout" class="btn btn-danger">Wyloguj się</button>
            </form>
      
        <br>
        <div class="row">
            <div class="col">
            
                <?php if (count($lines) > 0) : ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Discord ID</th>
                            <th scope="col">Nickname na discord</th>
                            <th scope="col">Data</th>
                            <th scope="col">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lines as $line) : ?>
                            <?php $parts = explode("|", $line); ?>
                            <tr>
                                <td><?php echo $parts[0]; ?></td>
                                <td><?php echo $parts[1]; ?></td>
                                <td><?php echo $parts[2]; ?></td>
                                <td><?php echo $parts[3]; ?></td>
                                <td>
                                    <form method="post">
                                        <input type="hidden" name="id_to_delete" value="<?php echo $parts[0]; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger">Usuń</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else : ?>
                    <p>Brak danych do wyświetlenia.</p>
                <?php endif; ?>
            </div>
        </div>

    </div> <!-- Prawy banner -->
            <div class="col-md-2">
                <div class="card mb-4">
                    <img class="card-img-top" src="ss2.png" alt="Right Banner">
                </div>
            </div>
        </div>
    </div>
</div>     <?php include 'footer.php'; ?></div>    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
</body>
</html>


