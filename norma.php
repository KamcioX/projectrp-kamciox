<?php
session_start();

// Check if the user is logged in as an administrator
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: admin_login.php");
    exit();
}

$filename = 'typki.json';

// Function to check norm
function checkNorma($rzadowki, $reporty, $specty) {
    return ($rzadowki >= 15 && $reporty >= 15 && $specty >= 500);
}

// Handle form submission for adding a new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nick_discord'])) {
    $nick_discord = $_POST["nick_discord"];
    $rzadowki = isset($_POST["rzadowki"]) ? calculateExpression($_POST["rzadowki"]) : 0;
    $reporty = isset($_POST["reporty"]) ? calculateExpression($_POST["reporty"]) : 0;
    $specty = isset($_POST["specty"]) ? calculateExpression($_POST["specty"]) : 0;
    $rank = $_POST["rank"];
    $urlop = isset($_POST["urlop"]) ? true : false;

    $data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

    $data[] = [
        'nick_discord' => $nick_discord,
        'rzadowki' => $rzadowki,
        'reporty' => $reporty,
        'specty' => $specty,
        'rank' => $rank,
        'urlop' => $urlop
    ];

    file_put_contents($filename, json_encode($data));

    header("Location: norma.php");
    exit();
}

// Handle delete action
if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    $data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

    if (isset($data[$index])) {
        unset($data[$index]);
        $data = array_values($data);
        file_put_contents($filename, json_encode($data));
    }

    header("Location: norma.php");
    exit();
}

// Function to calculate expressions like "1+1", "2-1", etc.
function calculateExpression($expression) {
    eval('$result = ' . $expression . ';');
    return $result;
}

// Handle edit action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_index'])) {
    $index = $_POST['edit_index'];
    $rzadowki = isset($_POST["rzadowki"]) ? $_POST["rzadowki"] : 0;
    $reporty = isset($_POST["reporty"]) ? $_POST["reporty"] : 0;
    $specty = isset($_POST["specty"]) ? $_POST["specty"] : 0;
    $rank = $_POST["rank"];
    $urlop = isset($_POST["urlop"]) ? true : false;

    $data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

    if (isset($data[$index])) {
        $data[$index]['rzadowki'] = $rzadowki;
        $data[$index]['reporty'] = $reporty;
        $data[$index]['specty'] = $specty;
        $data[$index]['rank'] = $rank;
        $data[$index]['urlop'] = $urlop;
    }

    file_put_contents($filename, json_encode($data));

    header("Location: norma.php");
    exit();
}

// Read data for display
$data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

// List of ranks
$ranks = [
    "Trial Support", "Junior Support", "Support", "Senior Support",
    "Junior Moderator", "Moderator", "Senior Moderator", "Head Moderator"
];

// Sort data by rank
usort($data, function($a, $b) use ($ranks) {
    return array_search($a['rank'], $ranks) - array_search($b['rank'], $ranks);
});

// Handle reset action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_stats'])) {
    $data = file_exists($filename) ? json_decode(file_get_contents($filename), true) : [];

    foreach ($data as &$entry) {
        $entry['rzadowki'] = 0;
        $entry['reporty'] = 0;
        $entry['specty'] = 0;
    }

    file_put_contents($filename, json_encode($data));

    header("Location: norma.php");
    exit();
}

?>


<!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <title>Norma Tygodniowa - KamcioX ProjectRP</title>
</head>
<?php include 'navbar.php'; ?>
<body>
<div class="container-fluid">
    <div class="jumbotron">
        <h1 class="display-4">Norma Tygodniowa</h1>
        <p class="lead">Dodaj i zarządzaj normami użytkowników.</p>
        <hr class="my-4">
        <form action="norma.php" method="post">
            <div class="form-group">
                <label for="nick_discord">Nick Discord</label>
                <input type="text" class="form-control" id="nick_discord" name="nick_discord" required>
            </div>
            <div class="form-group">
                <label for="rzadowki">Ilość rządówek</label>
                <input type="text" class="form-control" id="rzadowki" name="rzadowki" value="0" required>
            </div>
            <div class="form-group">
                <label for="reporty">Ilość reportów</label>
                <input type="text" class="form-control" id="reporty" name="reporty" value="0" required>
            </div>
            <div class="form-group">
                <label for="specty">Ilość spectów</label>
                <input type="text" class="form-control" id="specty" name="specty" value="0" required>
            </div>
            <div class="form-group">
                <label for="rank">Ranga</label>
                <select class="form-control" id="rank" name="rank" required>
                    <?php foreach ($ranks as $rank): ?>
                        <option value="<?= htmlspecialchars($rank) ?>"><?= htmlspecialchars($rank) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="urlop" name="urlop">
                <label class="label" for="urlop">Urlop</label>
            </div>
            <button type="submit" class="btn btn-primary">Zapisz</button>
        </form>

        <hr class="my-4">
        <h2>Lista Administracji</h2>
        <div class="form-group">
            <label for="filter_rank">Filtruj według rangi</label>
            <select class="form-control" id="filter_rank">
                <option value="">Wszystkie</option>
                <?php foreach ($ranks as $rank): ?>
                    <option value="<?= htmlspecialchars($rank) ?>"><?= htmlspecialchars($rank) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nick Discord</th>
                    <th>Ilość rządówek</th>
                    <th>Ilość reportów</th>
                    <th>Ilość spectów</th>
                    <th>Ranga</th>
                    <th>Check normy</th>
                    <th>Urlop</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody id="userTable">
    <?php if (!empty($data)): ?>
        <?php foreach ($data as $index => $entry): ?>
            <tr>
                <td><?= htmlspecialchars($entry['nick_discord']) ?></td>
                <td><?= htmlspecialchars($entry['rzadowki']) ?></td>
                <td><?= htmlspecialchars($entry['reporty']) ?></td>
                <td><?= htmlspecialchars($entry['specty']) ?></td>
                <td><?= htmlspecialchars($entry['rank']) ?></td>
                <td><?= checkNorma($entry['rzadowki'], $entry['reporty'], $entry['specty']) ? '✔️' : '❌' ?></td>
                <td><?= $entry['urlop'] ? '✔️' : '❌' ?></td>
                <td>
                    <button type="button" class="btn btn-warning btn-sm edit-btn" data-index="<?= $index ?>" data-toggle="modal" data-target="#editModal">Edytuj</button>
                    <a href="norma.php?delete=<?= $index ?>" class="btn btn-danger btn-sm">Usuń</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="8">Brak zawartości do wyświetlenia.</td></tr>
    <?php endif; ?>
</tbody>
        </table>
        <form action="norma.php" method="post" style="margin-top: 20px;">
            <input type="hidden" name="reset_stats" value="1">
            <button type="submit" class="btn btn-danger">Wyzeruj statystyki</button> <a href="admin_panel.php" class="btn btn-secondary">Wróć do Panelu</a> <hr class="my-4">
        </form>
    
</div>

<!-- Modal for Editing -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edytuj zawartość</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" action="norma.php" method="post">
                <div class="modal-body">
                    <input type="hidden" id="edit_index" name="edit_index" value="">
                    <div class="form-group">
                        <label for="edit_rzadowki">Ilość rządówek</label>
                        <input type="text" class="form-control" id="edit_rzadowki" name="rzadowki" pattern="^[0-9+\-*/ ()]*$" title="Please enter a valid number or mathematical expression." required>
                    </div>
                    <div class="form-group">
                        <label for="edit_reporty">Ilość reportów</label>
                        <input type="text" class="form-control" id="edit_reporty" name="reporty" pattern="^[0-9+\-*/ ()]*$" title="Please enter a valid number or mathematical expression." required>
                    </div>
                    <div class="form-group">
                        <label for="edit_specty">Ilość spectów</label>
                        <input type="text" class="form-control" id="edit_specty" name="specty" pattern="^[0-9+\-*/ ()]*$" title="Please enter a valid number or mathematical expression." required>
                    </div>
                    <div class="form-group">
                        <label for="edit_rank">Ranga</label>
                        <select class="form-control" id="edit_rank" name="rank" required>
                            <?php foreach ($ranks as $rank): ?>
                                <option value="<?= htmlspecialchars($rank) ?>"><?= htmlspecialchars($rank) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="edit_urlop" name="urlop">
                        <label class="label" for="edit_urlop">Urlop</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
                    <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathjs/9.4.4/math.min.js"></script>
<script>
    $(document).ready(function() {
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var index = button.data('index');
            var entry = <?= json_encode($data) ?>[index];

            var modal = $(this);
            modal.find('#edit_index').val(index);
            modal.find('#edit_rzadowki').val(entry.rzadowki);
            modal.find('#edit_reporty').val(entry.reporty);
            modal.find('#edit_specty').val(entry.specty);
            modal.find('#edit_rank').val(entry.rank);
            modal.find('#edit_urlop').prop('checked', entry.urlop);
        });

        $('#editForm').on('submit', function(event) {
            event.preventDefault(); // Zapobiega domyślnemu działaniu formularza

            // Obliczenia dla rzadowki
            var rzadowkiInput = $('#edit_rzadowki');
            var rzadowkiValue = rzadowkiInput.val();
            if (rzadowkiValue.trim() !== '') {
                try {
                    var result = math.evaluate(rzadowkiValue);
                    rzadowkiInput.val(result);
                } catch (error) {
                    alert('Błąd obliczeń dla rządówek: ' + error.message);
                }
            }

            // Obliczenia dla reportów
            var reportyInput = $('#edit_reporty');
            var reportyValue = reportyInput.val();
            if (reportyValue.trim() !== '') {
                try {
                    var result = math.evaluate(reportyValue);
                    reportyInput.val(result);
                } catch (error) {
                    alert('Błąd obliczeń dla reportów: ' + error.message);
                }
            }

            // Obliczenia dla spectów
            var spectyInput = $('#edit_specty');
            var spectyValue = spectyInput.val();
            if (spectyValue.trim() !== '') {
                try {
                    var result = math.evaluate(spectyValue);
                    spectyInput.val(result);
                } catch (error) {
                    alert('Błąd obliczeń dla spectów: ' + error.message);
                }
            }

            // Submit formularza
            this.submit();
        });
    });
</script>

<?php include 'footer.php'; ?>
</body>
</html>
