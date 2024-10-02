<?php
session_start();
require 'db.php'; // Plik zawierający połączenie z bazą danych

// Funkcja tłumacząca daty na język polski
function translateDateToPolish($date) {
    $english = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $polish = ['stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia'];
    
    return str_replace($english, $polish, $date);
}

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Jeśli nie jest zalogowany, przekieruj do strony logowania
    header("Location: login.php");
    exit();
}

$userProfile = null;
$comments = [];
$error = null;

// Obsługa wyszukiwania użytkownika
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_username'])) {
    $search_username = $_POST['search_username'];

    // Pobierz dane użytkownika z bazy danych, w tym profile_image
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userProfile = $result->fetch_assoc();

        // Pobierz tytuły użytkownika wraz z datą ich przyznania
        $sql = "SELECT title, title_date FROM user_titles WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userProfile['id']);
        $stmt->execute();
        $result = $stmt->get_result();

        $titles = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $titles[] = $row;
            }
        } else {
            // Jeśli użytkownik nie ma żadnych tytułów, dodaj domyślny tytuł "Nowicjusz"
            $titles[] = ['title' => "Brak dostępnych tytułów", 'title_date' => null];
        }
        
        // Pobierz komentarze użytkownika
        $sql = "SELECT uc.comment, uc.comment_date, u.username as commenter FROM user_comments uc JOIN users u ON uc.commenter_id = u.id WHERE uc.user_id = ? ORDER BY uc.comment_date DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userProfile['id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $comments[] = $row;
            }
        }
    } else {
        $error = "Nie znaleziono użytkownika o takim nicku.";
    }
}

// Obsługa dodawania komentarzy
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) && $userProfile) {
    $comment = $_POST['comment'];
    $userId = $userProfile['id'];
    $commenterId = $_SESSION['user_id'];

    // Sprawdź, czy użytkownik nie dodawał komentarza w ciągu ostatnich 24 godzin
    $sql = "SELECT comment_date FROM user_comments WHERE commenter_id = ? AND user_id = ? ORDER BY comment_date DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $commenterId, $userId);
    $stmt->execute();
    $stmt->store_result();

    $lastCommentDate = null;
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($lastCommentDate);
        $stmt->fetch();
    }

    $stmt->close();

    if ($lastCommentDate && (strtotime($lastCommentDate) + 86400) > time()) {
        $error = "Możesz dodać kolejny komentarz za 24 godziny.";
    } else {
        // Dodaj komentarz do bazy danych
        $sql = "INSERT INTO user_comments (user_id, commenter_id, comment, comment_date) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $userId, $commenterId, $comment);
        $stmt->execute();
        $stmt->close();

        // Przeładuj stronę, aby wyświetlić nowy komentarz
        header("Location: search.php");
        exit();
    }
}

// Ustaw odpowiednią klasę CSS dla rangi użytkownika
$roleClass = '';
if ($userProfile) {
    if (in_array($userProfile['role'], ['Support', 'Moderator'])) {
        $roleClass = 'support-moderator';
    } elseif (in_array($userProfile['role'], ['Administrator', 'Management'])) {
        $roleClass = 'admin-management';
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wyszukaj Profil - KamcioX ProjectRP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        .support-moderator {
            color: yellow;
        }
        .admin-management {
            color: red;
        }
    </style>
</head>
<body class="custom-bg-color"><!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">

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
                <h1 class="display-4">Wyszukaj Profil Użytkownika</h1>
                <p class="lead">Wprowadź nick użytkownika, aby zobaczyć jego profil.</p>
                <hr class="my-4">
                <form method="POST" action="search.php">
                    <div class="form-group">
                        <label for="search_username">Nick użytkownika:</label>
                        <input type="text" class="form-control" id="search_username" name="search_username" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Wyszukaj</button>
                </form>
                <br>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($userProfile): ?>
                    <div class="jumbotron">
                        <h1 class="display-4">Profil użytkownika</h1>
                        <p class="lead">Poniżej znajdują się dane konta użytkownika.</p>
                        <hr class="my-4"> <!-- Treść główna -->
                        <div class="profile-image text-center">
    <?php if (!empty($userProfile['profile_image'])): ?>
        <img src="<?php echo htmlspecialchars($userProfile['profile_image']); ?>" alt="Zdjęcie profilowe" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
    <?php else: ?>
        <img src="default_profile.png" alt="Domyślne zdjęcie profilowe" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
    <?php endif; ?>
</div><br>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" colspan="2">Informacje o użytkowniku</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Nick</th>
                                    <td><span class="<?php echo $roleClass; ?>"><?php echo htmlspecialchars($userProfile['username'] ?? ''); ?></span></td>
                                </tr>
                                <tr>
                                    <th>Data urodzenia</th>
                                    <td><?php echo htmlspecialchars($userProfile['birthdate'] ?? 'Brak zawartości'); ?></td>
                                </tr>
                                <tr>
                                    <th>Rola</th>
                                    <td><?php echo htmlspecialchars($userProfile['role'] ?? ''); ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <br>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Tytuł</th>
                                    <th scope="col">Data przyznania</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($titles as $title): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($title['title']); ?></td>
                                        <td><?php echo htmlspecialchars($title['title_date'] ? translateDateToPolish(date("d F Y", strtotime($title['title_date']))) : 'Brak danych'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

     
                    </div>
                <?php endif; ?>
            </div> <!-- Prawy banner -->
            <div class="col-md-2">
                <div class="card mb-4">
                    <img class="card-img-top" src="ss2.png" alt="Right Banner">
                </div>
            </div>   
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3MiykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
