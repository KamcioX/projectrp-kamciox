<?php
session_start();
include 'questions.php';

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Obsługa wylogowania
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Sprawdź, czy zmienne sesji są zainicjalizowane, jeśli nie, zainicjalizuj je
if (!isset($_SESSION['questions'])) {
    shuffle($questions);
    $_SESSION['questions'] = array_slice($questions, 0, 10);
    $_SESSION['current_question'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['completed'] = false;  // Dodanie flagi ukończenia quizu
}

// Sprawdź, czy zmienna current_question jest ustawiona
if (!isset($_SESSION['current_question'])) {
    $_SESSION['current_question'] = 0;
}

// Obsługa odpowiedzi na pytania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $current_question_index = $_SESSION['current_question'];
    $selected_answer = intval($_POST['answer']);
    
    if ($current_question_index < count($_SESSION['questions'])) {
        $correct_answer = $_SESSION['questions'][$current_question_index]['correct'];

        if ($selected_answer === $correct_answer) {
            $_SESSION['score']++;
        }

        $_SESSION['current_question']++;

        if ($_SESSION['current_question'] >= count($_SESSION['questions'])) {
            $_SESSION['completed'] = true;  // Ustawienie flagi ukończenia quizu
            $score = $_SESSION['score'];
            $total_questions = count($_SESSION['questions']);
            $percentage = round(($score / $total_questions) * 100);

            $result = "<div class='alert alert-success' role='alert'>";
            $result .= "Ukończyłeś egzamin z wynikiem $percentage%.";
            $result .= "</div>";
            $result .= "<a href='main.php' class='btn btn-primary mt-3'>Strona Główna</a>";

            $_SESSION['result'] = $result;  // Przechowywanie wyniku w zmiennej sesji
        }
    }
}

$current_question_index = $_SESSION['current_question'];
$current_question = isset($_SESSION['questions'][$current_question_index]) ? $_SESSION['questions'][$current_question_index] : null;
?>
<!-- Custom CSS for Dark Mode -->
<link rel="stylesheet" href="style.css">
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"><link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
   <link rel="stylesheet" href="style.css"> <title>Egzamin - KamcioX ProjectRP</title>
    <script>
function highlightWord() {
    var searchText = document.getElementById("search-word").value.trim();
    
    // Sprawdź, czy pole wyszukiwania nie jest puste
    if (searchText === "") {
        alert("Pole wyszukiwania nie może być puste!");
        return;
    }
    
    var contentElement = document.getElementById("regulations");
    var content = contentElement.innerHTML;

    // Usuń istniejące podświetlenie
    var highlightedContent = content.replace(/<span style="background-color: yellow;">(.*?)<\/span>/g, '$1');

    // Use a temporary div to safely parse and manipulate the HTML
    var tempDiv = document.createElement("div");
    tempDiv.innerHTML = highlightedContent;

    // Function to recursively highlight text nodes
    function highlightNodes(node) {
        if (node.nodeType === 3) { // Text node
            var regex = new RegExp(searchText, 'gi');
            var parent = node.parentNode;
            var newHTML = node.nodeValue.replace(regex, match => `<span style="background-color: yellow;">${match}</span>`);
            var newSpan = document.createElement("span");
            newSpan.innerHTML = newHTML;
            parent.replaceChild(newSpan, node);
        } else if (node.nodeType === 1 && node.nodeName !== "SCRIPT" && node.nodeName !== "STYLE") { // Element node, not script or style
            for (var i = 0; i < node.childNodes.length; i++) {
                highlightNodes(node.childNodes[i]);
            }
        }
    }

    // Start the highlighting process
    highlightNodes(tempDiv);

    // Replace the original content with the highlighted content
    contentElement.innerHTML = tempDiv.innerHTML;
}
</script>
</head><?php include 'navbar.php'; ?>
<body>
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
        <h1 class="display-4">Próbny egzamin z administratorowania</h1>
        <p class="lead">Sprawdź siebie czy jesteś idealnym administratorem! Jak będziesz mieć mniej niż 95% to pakuj walize</p>
        <hr class="my-4">

        <?php
        if (isset($_SESSION['completed']) && $_SESSION['completed'] === true) {
            echo $_SESSION['result'];
            // Usuwanie tylko zmiennych sesji związanych z quizem
            unset($_SESSION['questions']);
            unset($_SESSION['current_question']);
            unset($_SESSION['score']);
            unset($_SESSION['completed']);
            unset($_SESSION['result']);
        } else {
        ?>

        <p id="regulations">
        <form method="post" action="quiz.php">
        <div class="quiz">
            <div class="card-body">
                <?php if ($current_question): ?>
                    <p style="color: black;"><?php echo htmlspecialchars($current_question['question'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php foreach ($current_question['answers'] as $index => $answer): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answer" id="answer<?php echo $index; ?>" value="<?php echo $index; ?>" required>
                            <label class="form-check-label" for="answer<?php echo $index; ?>">
                                <?php echo htmlspecialchars($answer, ENT_QUOTES, 'UTF-8'); ?>
                            </label>
                        </div>
                    <?php endforeach; ?> <div class="pytania">
            <?php echo "Jesteś na pytaniu " . ($_SESSION['current_question'] + 1) . " z " . count($_SESSION['questions']); ?>
        </div>
                <?php else: ?>
                    <div class="alert alert-danger" role="alert">
                        Nie udało się załadować pytania. Proszę spróbować ponownie.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Następne pytanie</button>
        </form>
        <form method="post" action="quiz.php" class="mt-3">
        <a href="main.php" class="btn btn-primary">Strona Główna</a>  <button type="submit" name="logout" class="btn btn-danger">Wyloguj</button>
        </form>

        <?php } // Zamyka else ?>

    </div><!-- Prawy banner -->
     <div class="col-md-2">
                <div class="card mb-4">
                    <img class="card-img-top" src="ss2.png" alt="Right Banner">
                </div>
            </div>
        </div>
    </div>
</div>   
</div>
<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
