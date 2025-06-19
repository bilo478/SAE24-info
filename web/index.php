<?php
require_once("db_config.php");

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) exit("Connexion échouée : " . $conn->connect_error);

// Récupérer la dernière mesure TOUTE catégorie confondue
$result = $conn->query("SELECT * FROM mesures ORDER BY timestamp DESC LIMIT 1");
$data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;

// Récupérer le nombre total de présences
$total = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM mesures WHERE LOWER(valeur) = '>>> présence détectée !'");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $total = $row['total'];
}

$conn->close();

// Initialisation des variables
$classeCouleur = 'rouge';
$message = "Aucune présence détectée";
$affichage = "Aucune détection enregistrée.";

if ($data) {
    $valeur = strtolower(trim($data['valeur']));
    $timestamp = $data['timestamp'];

    // Détermination du message de date
    $derniereDetection = new DateTime($timestamp);
    $now = new DateTime();
    $intervalle = $now->getTimestamp() - $derniereDetection->getTimestamp();

    // Formattage de l'affichage
    $diffJours = $now->diff($derniereDetection)->days;
    if ($derniereDetection->format('Y-m-d') === $now->format('Y-m-d')) {
        $affichage = "Aujourd'hui à " . $derniereDetection->format("H:i:s");
    } elseif ($diffJours === 1) {
        $affichage = "Hier à " . $derniereDetection->format("H:i:s");
    } else {
        $affichage = $derniereDetection->format("d/m/Y à H:i:s");
    }

    // Test : si la dernière valeur est "présence" ET elle date de moins de 10 secondes
    if ($valeur === '>>> présence détectée !' && $intervalle <= 10) {
        $classeCouleur = 'vert';
        $message = "Présence détectée";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suivi des présences - SAE24</title>
    <link rel="stylesheet" href="style.css">
    <script src="js/script.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Suivi des présences - SAE24</h1>
        <div class="carte rouge" id="carte">
            <h2 id="message">Chargement...</h2>
            <p class="total">Présence(s) totale(s) détectée(s) : <strong id="total">0</strong></p>
            <p class="timestamp" id="timestamp">Dernière détection : ...</p>
        </div>
        <div class="reset-zone">
            <button id="reset-btn">Réinitialiser les présences</button>
            <p id="reset-confirm">Table vidée.</p>
        </div>
    </div>
</body>
</html>