<?php
session_start();
require 'db.php';

// Ustawienie lokalizacji na polski
setlocale(LC_TIME, 'pl_PL.UTF-8');

// Sprawdź, czy użytkownik jest zalogowany jako administrator
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: admin_login.php");
    exit();
}

$error = "";
$success = "";

// Obsługa dodawania tytułu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["new_title"]) && isset($_POST["username"])) {
    $new_title = '« ' . $conn->real_escape_string($_POST["new_title"]) . ' »';
    $username = $conn->real_escape_string($_POST["username"]);
    $title_date = date('Y-m-d'); // Pobierz aktualną datę

    // Pobierz ID użytkownika na podstawie nazwy użytkownika
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Dodaj nowy tytuł do tabeli user_titles wraz z datą przyznania
        $sql = "INSERT INTO user_titles (user_id, title, title_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $new_title, $title_date);

        if ($stmt->execute()) {
            $success = "Tytuł został pomyślnie dodany.";
        } else {
            $error = "Błąd podczas dodawania tytułu: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Użytkownik nie istnieje.";
    }
}

// Obsługa usuwania tytułu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_title_id"])) {
    $title_id = $conn->real_escape_string($_POST["remove_title_id"]);

    // Usuń tytuł z tabeli user_titles
    $sql = "DELETE FROM user_titles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $title_id);

    if ($stmt->execute()) {
        $success = "Tytuł został pomyślnie usunięty.";
    } else {
        $error = "Błąd podczas usuwania tytułu: " . $stmt->error;
    }
    $stmt->close();
}

// Pobierz listę użytkowników i ich tytułów
$sql = "SELECT u.username, t.id AS title_id, t.title, t.title_date FROM users u LEFT JOIN user_titles t ON u.id = t.user_id";
$result = $conn->query($sql);

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[$row['username']]['titles'][] = [
            'title_id' => $row['title_id'],
            'title' => $row['title'],
            'title_date' => $row['title_date']
        ];
    }
}
?>

<!doctype html>
<html lang="pl">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <title>Zarządzanie Tytułami - KamcioX ProjectRP</title>
</head>
<!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">
<body>
<?php include 'navbar.php'; ?>
<div class="container-fluid">
    <div class="jumbotron">
        <h1 class="display-4">Zarządzanie Tytułami</h1>
        <p class="lead">Tutaj możesz dodawać lub usuwać tytuły dla użytkowników.</p>
        <hr class="my-4">

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nazwa użytkownika</th>
                    <th>Tytuły</th>
                    <th>Dodaj Tytuł</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $username => $user_data): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($username); ?></td>
                        <td>
                            <ul>
                                <?php if (!empty($user_data['titles'])): ?>
                                    <?php foreach ($user_data['titles'] as $title): ?>
                                        <?php if ($title['title'] !== null): ?>
                                            <li>
                                                <?php echo htmlspecialchars($title['title']); ?>
                                                <small><?php echo htmlspecialchars($title['title_date'] ? strftime("%e %B %Y", strtotime($title['title_date'])) : 'Brak daty'); ?></small>
                                                <form action="tytuly.php" method="post" class="d-inline">
                                                    <input type="hidden" name="remove_title_id" value="<?php echo htmlspecialchars($title['title_id']); ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Usuń</button>
                                                </form>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>Brak tytułów</li>
                                <?php endif; ?>
                            </ul>
                        </td>
                        <td>
                            <form action="tytuly.php" method="post" class="form-inline">
                                <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                                <input type="text" name="new_title" class="form-control mb-2 mr-sm-2" placeholder="Nowy tytuł" required>
                                <button type="submit" class="btn btn-primary mb-2">Dodaj</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3MiykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
