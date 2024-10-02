<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: admin_login.php");
    exit();
}

$filename = 'trial_support.json';

// Function to format the date in Polish
function formatDateTime($date) {
    setlocale(LC_TIME, 'pl_PL.UTF-8');
    $months_polish = [
        'January' => 'stycznia',
        'February' => 'lutego',
        'March' => 'marca',
        'April' => 'kwietnia',
        'May' => 'maja',
        'June' => 'czerwca',
        'July' => 'lipca',
        'August' => 'sierpnia',
        'September' => 'września',
        'October' => 'października',
        'November' => 'listopada',
        'December' => 'grudnia'
    ];
    
    $formatted_date = strftime('%e %B %Y, %H:%M', strtotime($date));
    foreach ($months_polish as $english => $polish) {
        $formatted_date = str_replace($english, $polish, $formatted_date);
    }
    return $formatted_date;
}

// Handle form submission for adding a new support
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nick_discord'])) {
    $nick_discord = $_POST["nick_discord"];
    $data_dolaczenia = date('Y-m-d H:i:s');
    $data_zakonczenia = date('Y-m-d H:i:s', strtotime($data_dolaczenia . ' + 7 days'));

    $data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

    $data[] = [
        'nick_discord' => $nick_discord,
        'data_dolaczenia' => $data_dolaczenia,
        'data_zakonczenia' => $data_zakonczenia
    ];

    file_put_contents($filename, json_encode($data));
}

// Handle form submission for editing a support's joining date
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_index'])) {
    $edit_index = $_POST["edit_index"];
    $data_dolaczenia = $_POST["data_dolaczenia"];
    $data_zakonczenia = date('Y-m-d H:i:s', strtotime($data_dolaczenia . ' + 7 days'));

    $data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

    if (isset($data[$edit_index])) {
        $data[$edit_index]['data_dolaczenia'] = $data_dolaczenia;
        $data[$edit_index]['data_zakonczenia'] = $data_zakonczenia;
        file_put_contents($filename, json_encode($data));
    }

    header("Location: trial_support.php");
    exit();
}

// Handle delete action
if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    $data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

    if (isset($data[$index])) {
        unset($data[$index]);
        file_put_contents($filename, json_encode(array_values($data)));
    }

    header("Location: trial_support.php");
    exit();
}

// Read data for display
$data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];
?><!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Trial Support - KamcioX ProjectRP</title>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container-fluid">
    <div class="jumbotron">
        <h1 class="display-4">Zarządzaj Trial Supportami</h1>
        <p class="lead">Dodaj i zarządzaj trial supportami.</p>
        <hr class="my-4">
        <form action="trial_support.php" method="post">
            <div class="form-group">
                <label for="nick_discord">Nick Discord</label>
                <input type="text" class="form-control" id="nick_discord" name="nick_discord" required>
            </div>
            <button type="submit" class="btn btn-primary">Zapisz</button> <a href="admin_panel.php" class="btn btn-secondary">Wróć do Panelu</a>
        </form>
        <hr class="my-4">
        <h2>Lista Trial Supportów</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nick Discord</th>
                    <th>Data Dołączenia</th>
                    <th>Data Zakończenia</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $index => $entry): ?>
                        <tr>
                            <td><?= htmlspecialchars($entry['nick_discord']) ?></td>
                            <td><?= formatDateTime($entry['data_dolaczenia']) ?></td>
                            <td><?= formatDateTime($entry['data_zakonczenia']) ?></td>
                            <td>
                                <form action="trial_support.php" method="post" style="display:inline;">
                                    <input type="hidden" name="edit_index" value="<?= $index ?>">
                                    <input type="datetime-local" name="data_dolaczenia" value="<?= date('Y-m-d\TH:i', strtotime($entry['data_dolaczenia'])) ?>" required>
                                    <button type="submit" class="btn btn-success btn-sm">Edytuj</button>
                                </form>
                                <a href="trial_support.php?delete=<?= $index ?>" class="btn btn-danger btn-sm">Usuń</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">Brak zawartości do wyświetlenia.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div><?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
