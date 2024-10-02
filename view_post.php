<?php
session_start();
require 'db.php';

// Sprawdzenie, czy użytkownik jest zalogowany i czy sesja zawiera username
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username']; // Pobranie nazwy użytkownika z sesji

// Pobieranie ID użytkownika na podstawie nazwy użytkownika
$sql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Sprawdzenie, czy post_id jest ustawione
if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
    header("Location: index.php");
    exit();
}

$post_id = intval($_GET['post_id']);

// Pobieranie postu z bazy danych
$sql = "SELECT p.*, u.username, 
               (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count 
        FROM posts p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Sprawdzenie, czy użytkownik polubił post
$sql = "SELECT * FROM likes WHERE post_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$liked = $stmt->get_result()->num_rows > 0;
$stmt->close();

// Pobieranie komentarzy do postu
$sql = "SELECT c.comment, u.username, c.created_at 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.post_id = ? 
        ORDER BY c.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$comments = $stmt->get_result();
?>
<link rel="stylesheet" href="style.css">
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wyświetl post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 20px;
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
    </style>
</head>
<body><?php include 'navbar.php'; ?>

<div class="container">
    <div class="jumbotron">
        <div class="card post">
            <div class="card-body">
                <h2 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="Post Image" class="img-fluid">
                <br><h5>Autor: <?php echo htmlspecialchars($post['username']); ?></h5><p class="card-text mt-2"><?php echo htmlspecialchars($post['description']); ?></p>

                <!-- Polubienia -->
                <?php
                $like_count = $post['like_count'];
                ?>

<form method="POST" action="like.php" style="display:inline;">
    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
    <button type="button" class="btn btn-light like-button" data-post-id="<?php echo $post_id; ?>">
        <?php echo $liked ? "❤️" : "🤍"; ?>
        <span><?php echo $like_count; ?></span>
    </button>
</form>
                
                <!-- Komentarze -->
                <h5 class="mt-4">Komentarze</h5>
                <div class="comments">
                    <?php while ($comment = $comments->fetch_assoc()): ?>
                        <div class="comment">
                            <strong><?php echo htmlspecialchars($comment['username']); ?></strong> <?php
                            // Formatowanie daty do formatu dd/mm/yyyy o hh:mm
                            $formatted_date = date('d/m/Y \o H:i', strtotime($comment['created_at']));
                            ?>
                            <small class="text-muted"><?php echo $formatted_date; ?></small>
                            <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                           
                        </div>
                    <?php endwhile; ?>
                </div>

                <form method="POST" action="comment.php">
                    <div class="form-group">
                        <textarea class="form-control" name="comment" placeholder="Dodaj komentarz..." required></textarea>
                        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Dodaj komentarz</button>       
                    <a href="instagram.php" class="btn btn-info mt-2 btn-lg btn-block">Wróć na instagrama</a>
                </form>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script><script>
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
</script>
</body>
</html>
