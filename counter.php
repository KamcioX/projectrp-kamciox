<?php
$counter_file = 'counter.txt';

// Funkcja zapisująca licznik do pliku
function saveCounter($counter) {
    global $counter_file;
    file_put_contents($counter_file, $counter);
}

// Funkcja odczytująca licznik z pliku
function getCounter() {
    global $counter_file;
    return (int)file_get_contents($counter_file);
}

// Inkrementacja licznika i zapis do pliku
$counter = getCounter() + 1;
saveCounter($counter);

// Zwrócenie liczby wyświetleń do wyświetlenia na stronie
echo "Liczba wyświetleń: " . $counter;
?>
