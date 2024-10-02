<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Strona Główna - KamcioX ProjectRP</title>
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
                    <h1 class="display-4 text-center">Status serwera</h1>
                    <p class="lead text-center">Zobacz informacje o serwerze.</p>
                    <hr class="my-4">

                    <?php
                    // Konfiguracja nagłówków dla requesta
                    $opts = [
                        'http' => [
                            'method' => 'GET',
                            'header' => [
                                "User-Agent: PHP-Script\r\n"
                            ]
                        ]
                    ];

                    $context = stream_context_create($opts);

                    // Pobranie danych z API
                    $players_json = @file_get_contents('https://proxy.KamcioX ProjectRP.pl/players.json', false, $context);

                    if ($players_json === FALSE) {
                        echo "<h2 class='text-center text-danger'>Nie udało się połączyć z API serwera.</h2>";
                        $player_count = 0;
                        $players_data = [];
                    } else {
                        $players_data = json_decode($players_json, true);
                        if (is_array($players_data)) {
                            $player_count = count($players_data);
                        } else {
                            echo "<h2 class='text-center text-danger'>Błąd w przetwarzaniu danych z API.</h2>";
                            $player_count = 0;
                            $players_data = [];
                        }
                    }
                    ?>
                    
                    <!-- Wyświetlanie liczby graczy -->
                    <h2 class="text-center">Liczba aktywnych graczy: <?php echo $player_count; ?>/500</h2>
                    
                    <!-- Tabela graczy -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Nick</th>
                                    <th scope="col">Discord</th>
                                    <th scope="col">ID w grze</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($player_count > 0) {
                                    foreach ($players_data as $player) {
                                        $name = htmlspecialchars($player['name'], ENT_QUOTES, 'UTF-8');
                                        $discord = '';
                                        foreach ($player['identifiers'] as $identifier) {
                                            if (strpos($identifier, 'discord:') === 0) {
                                                $discord = htmlspecialchars(substr($identifier, 8), ENT_QUOTES, 'UTF-8');
                                                break;
                                            }
                                        }
                                        $id_in_game = htmlspecialchars($player['id'], ENT_QUOTES, 'UTF-8');
                                        
                                        echo "<tr>";
                                        echo "<td>$name</td>";
                                        echo "<td>$discord</td>";
                                        echo "<td>$id_in_game</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center'>Brak dostępnych danych.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <br>
                    <div class="text-center">
                        <a href="login.php" class="btn btn-primary">Logowanie</a>
                        <a href="register.php" class="btn btn-primary">Rejestracja konta</a>
                        <a href="https://cfx.re/join/myxmk7" class="btn btn-secondary">Połącz z serwerem</a>
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
    
    <?php include 'footer.php'; ?>
    
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
