<?php
session_start();
require 'db.php';

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Pobierz user_id zalogowanego użytkownika
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
$stmt->execute([$_SESSION['username']]);
$user_id = $stmt->fetchColumn();

if (!$user_id) {
    echo "Nie znaleziono user_id dla użytkownika.";
    exit;
}

// Liczba postów na stronę
$posts_per_page = 6;

// Sprawdź, czy parametr 'page' został przekazany w URL, jeśli nie, ustaw na 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Oblicz offset (początek wyników)
$offset = ($page - 1) * $posts_per_page;

// Pobieranie danych z bazy danych z uwzględnieniem limitu i offsetu
$stmt = $pdo->prepare('SELECT * FROM postsgielda ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $posts_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pobierz łączną liczbę postów, aby obliczyć liczbę stron
$total_posts_stmt = $pdo->query('SELECT COUNT(*) FROM postsgielda');
$total_posts = $total_posts_stmt->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);
?>
<style>
    .img-fixed {
        width: 100%; /* Szerokość obrazka dostosowana do szerokości karty */
        height: 300px; /* Stała wysokość */
        object-fit: contain; /* Obrazek będzie zmniejszony, ale zachowa proporcje i wyświetli się w całości */
        background-color: #181c21; /* Opcjonalnie można dodać tło, jeśli obraz nie wypełni całego obszaru */
    }
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Giełda - KamcioX ProjectRP</title>
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
                    <h1 class="display-4 text-center">Giełda</h1>
                    <p class="lead text-center">Poniżej znajdują się ogłoszenia użytkowników ze swoimi perełkami!</p>
                    <hr> 
                    <a href="upload_auto.php" class="btn btn-danger mt-2 btn-lg btn-block">Wystaw auto na sprzedaż</a> 
                    <a href="ranking.php" class="btn btn-danger mt-2 btn-lg btn-block">Wystaw mieszkanie na sprzedaż</a>
                    <hr class="my-4">
                    
                    <div class="row">
                        <?php foreach ($posts as $post): ?>
                            <div class="col-md-4">
                                <div class="card mb-4">
                                    <img class="card-img-top img-fixed" src="<?= htmlspecialchars($post['image_path']) ?>" alt="Obrazek">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($post['description']) ?></p>
                                        <p class="card-text"><b>Cena: <?= htmlspecialchars($post['price']) ?>$</b></p>
                                        <p class="card-text">
                                           FullTune: <?= htmlspecialchars($post['full_tune']) === 'yes' ? 'Tak' : 'Nie' ?><br>
                                           Full Wizualka: <?= htmlspecialchars($post['full_visual']) === 'yes' ? 'Tak' : 'Nie' ?><br>
                                            Możliwość negocjacji: <?= $post['is_negotiable'] === 'yes' ? 'Tak' : 'Nie' ?><br>
                                            Telefon IC: <?= htmlspecialchars($post['phone']) ?>
                                        </p>
                                        <a href="<?= htmlspecialchars($post['image_path']) ?>" class="btn btn-secondary btn-block" data-lightbox="image-<?= htmlspecialchars($post['id']) ?>">Pokaż pełne zdjęcie</a>
                                        <?php if ($post['user_id'] == $user_id): ?>
                                            <form action="delete_post_auto.php" method="post">
                                                <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">
                                                <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Czy na pewno chcesz usunąć ten post?')">Usuń</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Paginacja -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <!-- Przycisk poprzednia strona -->
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <!-- Wyświetlanie numerów stron -->
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <!-- Przycisk następna strona -->
                            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
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
</body>
</html>