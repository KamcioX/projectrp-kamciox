<?php
session_start();
require 'db.php';

// Sprawdzanie sesji użytkownika
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}


if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Dodawanie ogłoszenia do bazy danych
if (isset($_POST['submit_ad'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_SESSION['username']; // Zakładamy, że nick jest zapisany w sesji
    $date = date('Y-m-d H:i:s');

    // Sprawdzenie czasu od ostatniego ogłoszenia
    $stmt = $pdo->prepare("SELECT date FROM ads WHERE author = ? ORDER BY date DESC LIMIT 1");
    $stmt->execute([$author]);
    $last_ad = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($last_ad) {
        $last_ad_time = strtotime($last_ad['date']);
        $current_time = strtotime($date);
        $time_difference = $current_time - $last_ad_time;

        if ($time_difference < 12 * 60 * 60) { // 12 godzin w sekundach
            echo "<script>alert('Możesz dodać ogłoszenie tylko raz na 12 godzin. Spróbuj ponownie później.');</script>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO ads (title, content, author, date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $content, $author, $date]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO ads (title, content, author, date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $author, $date]);
    }
}

// Obsługa paginacji
$ads_per_page = 5;
$stmt = $pdo->query("SELECT COUNT(*) FROM ads");
$total_ads = $stmt->fetchColumn();
$total_pages = ceil($total_ads / $ads_per_page);

$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_index = ($current_page - 1) * $ads_per_page;

// Pobieranie ogłoszeń, sortowanie od najnowszego do najstarszego
$stmt = $pdo->prepare("SELECT * FROM ads ORDER BY date DESC LIMIT :start_index, :ads_per_page");
$stmt->bindValue(':start_index', $start_index, PDO::PARAM_INT);
$stmt->bindValue(':ads_per_page', $ads_per_page, PDO::PARAM_INT);
$stmt->execute();
$current_ads = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <title>Ogłoszenia - KamcioX ProjectRP</title>
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

                <!-- Treść główna -->
                <div class="col-md-8">
                    <h1 class="display-4">Wystaw Ogłoszenie</h1>
                    <p class="lead">Tutaj możesz wystawić nowe ogłoszenie.</p>
                    <hr class="my-4">

                    <form method="post" action="">
                        <div class="form-group">
                            <label for="title">Tytuł:</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="content">Treść:</label>
                            <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                        </div>
                        <button type="submit" name="submit_ad" class="btn btn-primary btn-block">Dodaj Ogłoszenie</button>
                    </form>

                    <hr class="my-4">
                    <h2 class="display-5">Lista Ogłoszeń</h2>

                    <?php if (!empty($current_ads)): ?>
                        <ul class="list-group">
                            <?php foreach ($current_ads as $ad): ?>
                                <?php
                                    // Pobieranie awatara użytkownika na podstawie jego nazwy użytkownika
                                    $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE username = ?");
                                    $stmt->execute([$ad['author']]);
                                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $avatar = !empty($user['profile_image']) ? $user['profile_image'] : 'default_profile.png';
                                ?>
                                <li class="list-group-item mb-3">
                                    <h5><?php echo htmlspecialchars($ad['title']); ?></h5>
                                    <p><?php echo htmlspecialchars($ad['content']); ?></p>
                                    <p class="text-muted">
                                        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" class="rounded-circle" width="40" height="40">
                                        Wystawione przez: <?php echo htmlspecialchars($ad['author']); ?> 
                                        | Data: <?php echo date('d/m/Y', strtotime($ad['date'])); ?>
                                    </p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Brak ogłoszeń.</p>
                    <?php endif; ?>

                    <!-- Paginacja -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center">
                            <?php if ($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>

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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

</body>

</html>