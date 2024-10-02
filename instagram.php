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

$username = $_SESSION['username'];
$sql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Stronowanie
$posts_per_page = 3; // Liczba postów na stronę
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

// Pobranie łącznej liczby postów
$sql = "SELECT COUNT(*) FROM posts";
$total_posts = $conn->query($sql)->fetch_row()[0];
$total_pages = ceil($total_posts / $posts_per_page);

// Pobieranie postów z bazy danych, wraz z liczbą polubień i komentarzy
$sql = "
    SELECT p.*, u.username, 
           (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count,
           (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $posts_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<link rel="stylesheet" href="style.css">
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram - KamcioX ProjectRP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .post {
            margin-bottom: 30px;
        }
        .post img {
            max-width: 1000px; /* Maksymalna szerokość */
            max-height: 500px; /* Maksymalna wysokość */
            width: auto; /* Automatyczna szerokość */
            height: auto; /* Automatyczna wysokość */
            object-fit: cover; /* Zachowuje proporcje zdjęcia */
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .card {
            margin-bottom: 20px;
        }
        /* Wyśrodkowanie kontenera paginacji */
        .pagination-container {
            display: flex;
            justify-content: center; /* Wyśrodkowanie w poziomie */
            margin-top: 20px; /* Opcjonalnie: margines górny dla lepszego wyglądu */
        }

        /* Dostosowanie stylów dla przycisków paginacji */
        .pagination {
            margin: 0;
            padding: 0;
            display: flex;
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
                <h1 class="display-4">Instagram (BETA)</h1>    
                <hr>   
                <a href="upload.php" class="btn btn-danger mt-2 btn-lg btn-block">Dodaj post</a> <a href="ranking.php" class="btn btn-info mt-2 btn-lg btn-block">Ranking</a>
                <hr class="my-4">

                <?php while ($post = $result->fetch_assoc()): ?>
                    <div class="card post">
                        <div class="card-body">
                            <h2 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                            <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Post Image" class="img-fluid">
                            <br>
                            <h6>Autor: <?php echo htmlspecialchars($post['username']); ?> </h6>
                            <p class="card-text mt-2"><?php echo htmlspecialchars($post['description']); ?></p>

                            <!-- Polubienia i Komentarze -->
                            <?php
                            $post_id = $post['id'];
                            $like_count = $post['like_count'];
                            $comment_count = $post['comment_count'];

                            // Pobranie użytkowników, którzy polubili post
                            $sql = "SELECT u.username FROM likes l JOIN users u ON l.user_id = u.id WHERE l.post_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $post_id);
                            $stmt->execute();
                            $result_likes = $stmt->get_result();
                            $usernames = [];
                            while ($row = $result_likes->fetch_assoc()) {
                                $usernames[] = $row['username'];
                            }
                            $stmt->close();

                            // Sprawdzenie, czy użytkownik już polubił post
                            $sql = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("ii", $post_id, $user_id);
                            $stmt->execute();
                            $liked = $stmt->get_result()->num_rows > 0;
                            ?>

                            <form method="POST" action="like.php" style="display:inline;">
                                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                                <button type="button" class="btn btn-light like-button" data-post-id="<?php echo $post_id; ?>">
                                    <?php echo $liked ? "❤️" : "🤍"; ?>
                                    <span><?php echo $like_count; ?></span>
                                </button>
                            </form>

                            <!-- Wyświetlanie nicków -->
                            <span>
    <?php
    if (count($usernames) > 2) {
        // Wyświetl pierwszych dwóch użytkowników i link do modalu
        echo htmlspecialchars($usernames[0]) . ", " . htmlspecialchars($usernames[1]) . ' <a href="#" data-toggle="modal" data-target="#likeModal-' . htmlspecialchars($post_id) . '"> oraz inni lubią to zdjęcie</a>';
    } elseif (count($usernames) == 2) {
        // Wyświetl dwóch użytkowników
        echo htmlspecialchars($usernames[0]) . " i " . htmlspecialchars($usernames[1]) . " lubią to zdjęcie";
    } elseif (count($usernames) == 1) {
        // Wyświetl jednego użytkownika
        echo htmlspecialchars($usernames[0]) . " lubi to zdjęcie";
    } else {
        // Brak polubień
        echo "Nikt jeszcze nie polubił tego zdjęcia";
    }
    ?>
</span>

                            <span class="ml-3">💬 <?php echo $comment_count; ?> komentarzy</span>

                            <!-- Modal -->
                            <div class="modal fade" id="likeModal-<?php echo $post_id; ?>" tabindex="-1" role="dialog" aria-labelledby="likeModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="likeModalLabel">Użytkownicy, którzy polubili ten post</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <ul>
                                                <?php foreach ($usernames as $username): ?>
                                                    <li><?php echo htmlspecialchars($username); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Przycisk wyświetlania postu -->
                            <hr>
                            <a href="view_post.php?post_id=<?php echo $post_id; ?>" class="btn btn-info mt-2 btn-lg btn-block">Wyświetl post</a>

                            <?php if ($post['user_id'] == $user_id): ?>
                                <!-- Formularz usuwania postu -->
                                <form method="POST" action="delete_post.php" onsubmit="return confirm('Czy na pewno chcesz usunąć ten post?');">
                                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                                    <button type="submit" class="btn btn-danger mt-2 btn-lg btn-block">Usuń post</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>

                <!-- Paginacja -->
                <div class="container">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">&laquo;</span>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <span class="page-link">&raquo;</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
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
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dodaj nasłuchiwacz do wszystkich przycisków "like"
    document.querySelectorAll('.like-button').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Zapobiega domyślnemu działaniu

            var post_id = this.dataset.postId;
            var likeButton = this;

            // Wysyłanie żądania AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'like.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        // Aktualizuj ikonę polubienia
                        if (response.liked) {
                            likeButton.innerHTML = '❤️ ' + response.like_count;
                        } else {
                            likeButton.innerHTML = '🤍 ' + response.like_count;
                        }
                    } else {
                        alert('Wystąpił błąd: ' + response.error);
                    }
                }
            };

            xhr.send('post_id=' + encodeURIComponent(post_id));
        });
    });
});
</script><script>
document.addEventListener('DOMContentLoaded', function() {
    // Funkcja aktualizująca dane o polubieniach
    function updateLikes(postId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_likes.php?post_id=' + encodeURIComponent(postId), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    var likeDisplay = document.querySelector('#likeDisplay-' + postId);
                    if (likeDisplay) {
                        likeDisplay.innerHTML = response.like_display;
                    }
                } else {
                    console.error('Error: ' + response.error);
                }
            }
        };
        xhr.send();
    }

    // Dodaj nasłuchiwacz do przycisków "like" (załaduj aktualizacje po kliknięciu)
    document.querySelectorAll('.like-button').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            var postId = this.dataset.postId;
            updateLikes(postId);
        });
    });
});
</script>
</body>
</html>