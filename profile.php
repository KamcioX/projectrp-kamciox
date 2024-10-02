<?php
session_start();
require 'db.php'; // Plik zawierający połączenie z bazą danych

// Funkcja tłumacząca daty na język polski
function translateDateToPolish($date) {
    $english = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $polish = ['stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia'];
    
    return str_replace($english, $polish, $date);
}

// Funkcja do przycinania obrazu do wymiarów 720x300 pikseli
function cropImage($sourcePath, $targetPath) {
    // Pobierz wymiary obrazu źródłowego
    list($sourceWidth, $sourceHeight, $sourceType) = getimagesize($sourcePath);

    // Wymiary docelowe
    $targetWidth = 720;
    $targetHeight = 300;

    // Utwórz obraz docelowy o wymiarach 720x300
    $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

    // W zależności od typu obrazu źródłowego, wczytaj go odpowiednią funkcją
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false; // Obsługa tylko JPEG, PNG i GIF
    }

    // Przycinanie obrazu do wymiarów 720x300
    $cropX = 0; // Początkowa pozycja X do przycięcia
    $cropY = 0; // Początkowa pozycja Y do przycięcia

    // Jeśli obraz jest zbyt szeroki, przycięj z boków
    if ($sourceWidth > $targetWidth) {
        $cropX = ($sourceWidth - $targetWidth) / 2;
    }

    // Jeśli obraz jest zbyt wysoki, przycięj z góry i dołu
    if ($sourceHeight > $targetHeight) {
        $cropY = ($sourceHeight - $targetHeight) / 2;
    }

    // Kopiowanie i przycięcie obrazu źródłowego do obrazu docelowego
    imagecopyresampled($targetImage, $sourceImage, 0, 0, $cropX, $cropY, $targetWidth, $targetHeight, $targetWidth, $targetHeight);

    // Zapisz obraz docelowy do pliku
    switch ($sourceType) {
        case IMAGETYPE_JPEG:
            imagejpeg($targetImage, $targetPath, 90); // Zapisz jako JPEG z kompresją 90%
            break;
        case IMAGETYPE_PNG:
            imagepng($targetImage, $targetPath, 9); // Zapisz jako PNG z maksymalną kompresją
            break;
        case IMAGETYPE_GIF:
            imagegif($targetImage, $targetPath); // Zapisz jako GIF
            break;
    }

    // Zwolnij pamięć zajmowaną przez obrazy
    imagedestroy($sourceImage);
    imagedestroy($targetImage);
}

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Jeśli nie jest zalogowany, przekieruj do strony logowania
    header("Location: login.php");
    exit();
}

// Debugging: Sprawdź, czy zmienna $_SESSION['username'] jest ustawiona
if (!isset($_SESSION['username'])) {
    echo "Błąd: Brak wartości 'username' w sesji.";
    exit();
}

$username = $_SESSION['username'];

// Pobierz dane użytkownika z bazy danych
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Obsłuż sytuację, gdy nie znaleziono użytkownika
    echo "Błąd: Nie można odnaleźć użytkownika.";
    exit();
}
$stmt->close();

// Pobierz tytuły użytkownika wraz z datą ich przyznania
$sql = "SELECT title, title_date FROM user_titles WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();

$titles = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $titles[] = $row;
    }
} else {
    // Jeśli użytkownik nie ma żadnych tytułów, dodaj domyślny tytuł "Brak dostępnych tytułów"
    $titles[] = ['title' => "Brak dostępnych tytułów", 'title_date' => null];
}
$stmt->close();

// Pobierz postacie użytkownika
$sql = "SELECT id, character_name, character_date FROM user_characters WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();


$characters = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $characters[] = $row;
    }
}
$stmt->close();

// Funkcja do generowania nowej unikalnej nazwy pliku
function generateFileName($directory, $baseName, $extension) {
    $counter = 1;
    $newFileName = $directory . $baseName . '_' . $counter . '.' . $extension;
    
    while (file_exists($newFileName)) {
        $counter++;
        $newFileName = $directory . $baseName . '_' . $counter . '.' . $extension;
    }
    
    return $newFileName;
}

// Obsługa zmiany e-maila
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['new_email'])) {
        $new_email = $_POST['new_email'];

        // Aktualizacja e-maila w bazie danych
        $sql = "UPDATE users SET email = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_email, $username);

        if ($stmt->execute()) {
            // Zaktualizuj zmienną $user, aby wyświetlić nowy e-mail
            $user['email'] = $new_email;
        } else {
            echo "Błąd podczas aktualizacji e-maila.";
        }
        $stmt->close();
    }

    if (isset($_FILES['profile_image'])) {
        // Pobierz ścieżkę do starego zdjęcia profilowego
        $oldProfileImage = $user['profile_image'];

        // Przygotuj ścieżkę do nowego zdjęcia profilowego
        $target_dir = "uploads/";
        $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $target_file = generateFileName($target_dir, 'profile', $extension);

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            // Zaktualizuj ścieżkę do zdjęcia w bazie danych
            $sql = "UPDATE users SET profile_image = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $target_file, $username);
            if ($stmt->execute()) {
                // Zaktualizuj zmienną $user, aby wyświetlić nowe zdjęcie
                $user['profile_image'] = $target_file;

                // Usuń stare zdjęcie profilowe, jeśli istnieje i nie jest to zdjęcie domyślne
                if (!empty($oldProfileImage) && $oldProfileImage != 'default_profile.png') {
                    if (file_exists($oldProfileImage)) {
                        unlink($oldProfileImage);
                    }
                }
            } else {
                echo "Błąd podczas aktualizacji zdjęcia profilowego.";
            }
            $stmt->close();
        } else {
            echo "Błąd podczas przesyłania pliku.";
        }
    }

    // Obsługa dodawania postaci użytkownika
    if (isset($_POST['character_name'])) {
        $character_name = $_POST['character_name'];
        $character_date = date('Y-m-d'); // Ustaw aktualną datę

        // Dodaj nową postać do bazy danych
        $sql = "INSERT INTO user_characters (user_id, character_name, character_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user['id'], $character_name, $character_date);

        if ($stmt->execute()) {
            // Przekieruj, aby uniknąć ponownego dodania po odświeżeniu
            header("Location: profile.php");
            exit();
        } else {
            echo "Błąd podczas dodawania postaci.";
        }
        $stmt->close();
    }

    // Obsługa usuwania postaci użytkownika
    if (isset($_POST['delete_character_id'])) {
        $delete_character_id = $_POST['delete_character_id'];

        // Usuń postać z bazy danych
        $sql = "DELETE FROM user_characters WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $delete_character_id, $user['id']);

        if ($stmt->execute()) {
            // Przekieruj, aby usunięcie było widoczne
            header("Location: profile.php");
            exit();
        } else {
            echo "Błąd podczas usuwania postaci.";
        }
        $stmt->close();
    }

    // Obsługa czyszczenia slajdów
    if (isset($_POST['clear_slides'])) {
        $sql = "DELETE FROM user_slides WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user['id']);
        if ($stmt->execute()) {
            header("Location: profile.php");
            exit();
        } else {
            echo "Błąd podczas czyszczenia slajdów.";
        }
        $stmt->close();
    }

    // Obsługa dodawania slajdu
    if (isset($_FILES['slide_image'])) {
        // Przygotuj ścieżkę do nowego slajdu
        $extension = pathinfo($_FILES['slide_image']['name'], PATHINFO_EXTENSION);
        $target_dir = "uploads/";
        $target_file = generateFileName($target_dir, 'slide', $extension);

        if (move_uploaded_file($_FILES['slide_image']['tmp_name'], $target_file)) {
            $sql = "INSERT INTO user_slides (user_id, slide_image) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $user['id'], $target_file);
            if ($stmt->execute()) {
                header("Location: profile.php");
                exit();
            } else {
                echo "Błąd podczas dodawania slajdu.";
            }
            $stmt->close();
        } else {
            echo "Błąd podczas przesyłania pliku.";
        }
    }
}

// Ustaw odpowiednią klasę CSS dla rangi użytkownika
$roleClass = '';
if (in_array($user['role'], ['Support', 'Moderator'])) {
    $roleClass = 'support-moderator';
} elseif (in_array($user['role'], ['Administrator', 'Management'])) {
    $roleClass = 'admin-management';
}

// Pobierz slajdy użytkownika
$sql = "SELECT * FROM user_slides WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$slides = [];
while ($row = $result->fetch_assoc()) {
    $slides[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil użytkownika - KamcioX ProjectRP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style>
        .support-moderator {
            color: yellow;
        }
        .admin-management {
            color: red;
        }
        .profile-image img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
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
                <h1 class="display-4">Profil użytkownika</h1>
                <p class="lead">Poniżej znajdują się dane Twojego konta.</p>
                <hr class="my-4">
                <table class="table">
    <thead>
        <tr>
            <th scope="col" colspan="2">Informacje o użytkowniku</th>
        </tr>
    </thead>
       <!-- Treść główna -->
       <div class="profile-image text-center">
    <?php if (!empty($user['profile_image'])): ?>
        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Zdjęcie profilowe" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
    <?php else: ?>
        <img src="default_profile.png" alt="Domyślne zdjęcie profilowe" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover;">
    <?php endif; ?>
</div>
                <br>

    <tbody>
        <tr>
            <th>Nick</th>
            <td><span class="<?php echo $roleClass; ?>"><?php echo htmlspecialchars($user['username'] ?? ''); ?></span></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo !empty($user['email']) ? htmlspecialchars($user['email']) : 'Brak zawartości'; ?></td>
        </tr>
        <tr>
            <th>Data urodzenia</th>
            <td><?php echo !empty($user['birthdate']) ? htmlspecialchars($user['birthdate']) : 'Brak zawartości'; ?></td>
        </tr>
        <tr>
            <th>Rola</th>
            <td><?php echo htmlspecialchars($user['role'] ?? ''); ?></td>
        </tr>
    </tbody>
</table>
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#changeEmailModal">
                    Zmień e-mail
                </button> <!-- Przycisk zmiany zdjęcia profilowego -->
<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#changeProfileImageModal">
    Zmień zdjęcie profilowe
</button>

<!-- Modal zmiany zdjęcia profilowego -->
<div class="modal fade" id="changeProfileImageModal" tabindex="-1" role="dialog" aria-labelledby="changeProfileImageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeProfileImageModalLabel">Zmień zdjęcie profilowe</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="profile.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="profile_image">Wybierz nowe zdjęcie:</label>
                        <input type="file" class="form-control-file" id="profile_image" name="profile_image" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                </form>
            </div>
        </div>
    </div>
</div></form>
              
                <br>  <br>
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
                                <td><?php echo $title['title_date'] ? translateDateToPolish(date("d F Y", strtotime($title['title_date']))) : 'Brak danych'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table><br>

                <table class="table">
    <thead>
        <tr>
            <th scope="col">Postać</th>
            <th scope="col">Data dodania</th>
            <th scope="col">Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($characters)): ?>
            <tr>
                <td colspan="3">Brak danych</td>
            </tr>
        <?php else: ?>
            <?php foreach ($characters as $character): ?>
                <tr>
                    <td><?php echo htmlspecialchars($character['character_name']); ?></td>
                    <td><?php echo translateDateToPolish(date("d F Y", strtotime($character['character_date']))); ?></td>
                    <td>
                        <form method="post" action="profile.php">
                            <input type="hidden" name="delete_character_id" value="<?php echo $character['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Usuń</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCharacterModal">
                    Dodaj postać
                </button>
   

                <!-- Modal zmiany e-mail -->
                <div class="modal fade" id="changeEmailModal" tabindex="-1" role="dialog" aria-labelledby="changeEmailModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changeEmailModalLabel">Zmień adres e-mail</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="profile.php">
                                    <div class="form-group">
                                        <label for="new_email">Nowy adres e-mail:</label>
                                        <input type="email" class="form-control" id="new_email" name="new_email" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal dodawania postaci -->
                <div class="modal fade" id="addCharacterModal" tabindex="-1" role="dialog" aria-labelledby="addCharacterModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCharacterModalLabel">Dodaj nową postać</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="profile.php">
                                    <div class="form-group">
                                        <label for="character_name">Nazwa postaci:</label>
                                        <input type="text" class="form-control" id="character_name" name="character_name" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Dodaj postać</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

             
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

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>