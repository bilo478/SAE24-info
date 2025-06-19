<?php
// Inclusion du fichier contenant les identifiants de connexion à la base de données
require_once("db_config.php");

// Connexion à la base de données MySQL
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Si la connexion échoue, on retourne une erreur en JSON et on arrête le script
if ($conn->connect_error) {
    echo json_encode(["error" => "Connexion échouée"]);
    exit;
}

// On récupère la dernière ligne de la table "mesures" (dernière détection, qu'elle soit présence ou absence)
$result = $conn->query("SELECT * FROM mesures ORDER BY timestamp DESC LIMIT 1");
$data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;

// On compte combien de fois une présence a été détectée dans toute la base
$res = $conn->query("SELECT COUNT(*) AS total FROM mesures WHERE LOWER(valeur) = '>>> présence détectée !'");
$row = $res && $res->num_rows > 0 ? $res->fetch_assoc() : ['total' => 0];
$total = $row['total'];

// Initialisation des valeurs par défaut
$etat = "aucune";
$timestamp = null;

// Si une ligne a bien été récupérée...
if ($data) {
    // On nettoie et met en minuscules la valeur pour éviter les erreurs dues à la casse ou aux espaces
    $valeur = strtolower(trim($data['valeur']));
    $timestamp = $data['timestamp'];

    // Si cette valeur correspond à une présence, on met à jour l’état
    if ($valeur === '>>> présence détectée !') {
        $etat = "présence";
    }
}

// On renvoie les données sous forme JSON, lisibles par le JavaScript côté client
echo json_encode([
    "etat" => $etat,
    "timestamp" => $timestamp,
    "total" => $total
]);
?>
