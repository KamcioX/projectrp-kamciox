<?php
session_start();
require 'db.php';

// Włącz raportowanie błędów
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Pobierz ranking użytkowników na podstawie łącznej liczby polubień ich postów oraz ich awatary
$sql = "
    SELECT u.username, u.profile_image, SUM(like_count) AS total_likes
    FROM (
        SELECT p.user_id, COUNT(l.id) AS like_count
        FROM posts p
        LEFT JOIN likes l ON p.id = l.post_id
        GROUP BY p.id
    ) AS post_likes
    JOIN users u ON post_likes.user_id = u.id
    GROUP BY u.id
    ORDER BY total_likes DESC
    LIMIT 10
";
$result = $conn->query($sql);
?><link rel="stylesheet" href="style.css">
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking użytkowników</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <style>
        .ranking-container {
            margin-top: 50px;
        }
        .ranking-table {
            margin: auto;
            width: 100%;
            max-width: auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        .user-info {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid">
    <div class="jumbotron">
        <div class="row">
            <!-- Lewy baner -->
            <div class="col-md-2">
                <div class="card">
                    <img class="card-img-top" src="ss1.png" alt="Left Banner">
                </div>
            </div>

            <!-- Główna sekcja -->
            <div class="col-md-8">
                <h1 class="display-4">Ranking uzbieranych serduszek</h1>
                <hr><a href="instagram.php" class="btn btn-info mt-2 btn-lg btn-block">Wróć na instagrama</a><hr>

                <table class="table table-bordered ranking-table">
                    <thead>
                        <tr>
                            <th>Pozycja</th>
                            <th>Użytkownik</th>
                            <th>Łączna liczba serduszek</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    $position = 1;
    while ($row = $result->fetch_assoc()):
        // Sprawdź, czy użytkownik ma ustawiony awatar, jeśli nie, użyj domyślnego
        $profile_image = !empty($row['profile_image']) ? htmlspecialchars($row['profile_image']) : 'default_profile.png';
    ?>
        <tr>
            <td><?php echo $position++; ?></td>
            <td>
                <div class="user-info">
                    <img src="<?php echo $profile_image; ?>" alt="Avatar" class="avatar">
                    <?php echo htmlspecialchars($row['username']); ?>
                </div>
            </td>
            <td><?php echo $row['total_likes']; ?></td>
        </tr>
    <?php endwhile; ?>
</tbody>
                </table>
            </div>

            <!-- Prawy baner -->
            <div class="col-md-2">
                <div class="card">
                    <img class="card-img-top" src="ss2.png" alt="Right Banner">
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>