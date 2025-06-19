<?php
require_once("db_config.php");

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    echo json_encode(["error" => "Connexion échouée"]);
    exit;
}

// Dernière ligne brute
$result = $conn->query("SELECT * FROM mesures ORDER BY timestamp DESC LIMIT 1");
$data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;

// Compter les présences
$res = $conn->query("SELECT COUNT(*) AS total FROM mesures WHERE LOWER(valeur) = '>>> présence détectée !'");
$row = $res && $res->num_rows > 0 ? $res->fetch_assoc() : ['total' => 0];
$total = $row['total'];

$etat = "aucune";
$timestamp = null;

if ($data) {
    $valeur = strtolower(trim($data['valeur']));
    $timestamp = $data['timestamp'];

    if ($valeur === '>>> présence détectée !') {
        $etat = "présence";
    }
}

echo json_encode([
    "etat" => $etat,
    "timestamp" => $timestamp,
    "total" => $total
]);
?>