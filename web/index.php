<?php
// Inclusion des paramètres de connexion à la base (définis dans db_config.php)
require_once("db_config.php");

// Connexion à la base de données via MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Vérification d’échec de connexion
if ($conn->connect_error) exit("Connexion échouée : " . $conn->connect_error);

// Requête SQL pour récupérer la dernière mesure enregistrée (présence ou absence), triée par date décroissante
$result = $conn->query("SELECT * FROM mesures ORDER BY timestamp DESC LIMIT 1");

// Si on a bien une ligne, on l’extrait sous forme de tableau associatif
$data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;

// Initialisation du compteur de présences détectées
$total = 0;

// Requête SQL pour compter uniquement les valeurs indiquant une présence détectée
$res = $conn->query("SELECT COUNT(*) AS total FROM mesures WHERE LOWER(valeur) = '>>> présence détectée !'");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $total = $row['total'];
}

// Fermeture de la connexion à la base
$conn->close();

// Préparation des variables d’affichage pour le HTML
$classeCouleur = 'rouge'; // couleur par défaut : rouge (absence)
$message = "Aucune présence détectée";
$affichage = "Aucune détection enregistrée.";

if ($data) {
    // Nettoyage de la valeur extraite de la base
    $valeur = strtolower(trim($data['valeur']));
    $timestamp = $data['timestamp'];

    // Création d’un objet DateTime pour la dernière détection et l’heure actuelle
    $derniereDetection = new DateTime($timestamp);
    $now = new DateTime();

    // Calcul du temps écoulé en secondes
    $intervalle = $now->getTimestamp() - $derniereDetection->getTimestamp();

    // Calcul du nombre de jours de différence
    $diffJours = $now->diff($derniereDetection)->days;

    // Construction du texte à afficher selon la date
    if ($derniereDetection->format('Y-m-d') === $now->format('Y-m-d')) {
        $affichage = "Aujourd'hui à " . $derniereDetection->format("H:i:s");
    } elseif ($diffJours === 1) {
        $affichage = "Hier à " . $derniereDetection->format("H:i:s");
    } else {
        $affichage = $derniereDetection->format("d/m/Y à H:i:s");
    }

    // Si la dernière valeur indique une présence et qu’elle est récente (moins de 10 sec)
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

    <!-- Feuille de style principale -->
    <link rel="stylesheet" href="style.css">

    <!-- Script JS principal qui gère l’actualisation automatique -->
    <script src="js/script.js" defer></script>

    <!-- Chart.js pour l’affichage du graphique -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <h1>Suivi des présences - SAE24</h1>

        <div class="ligne">
            <!-- Carte affichant l’état actuel -->
            <div class="carte rouge" id="carte">
                <h2 id="message">Chargement...</h2>
                <p class="total">Présence(s) totale(s) détectée(s) : <strong id="total">0</strong></p>
                <p class="timestamp" id="timestamp">Dernière détection : ...</p>
            </div>

            <!-- Carte contenant le graphique d’historique -->
            <div class="carte graphique">
                <h3 class="graphe-titre">Historique des présences</h3>
                <canvas id="chart"></canvas>
            </div>
        </div>

        <!-- Zone de réinitialisation (bouton + message) -->
        <div class="reset-zone">
            <button id="reset-btn">Réinitialiser les présences</button>
            <p id="reset-confirm">Table vidée.</p>
        </div>
    </div>
</body>
</html>
