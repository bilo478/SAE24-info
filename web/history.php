<?php
// Inclusion des constantes de configuration de la base de données
require_once("db_config.php");

// Connexion à la base de données MySQL
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Si la connexion échoue, on renvoie un tableau vide en JSON et on arrête le script
if ($conn->connect_error) {
echo json_encode([]);
exit;
}

// Requête pour récupérer les 20 dernières lignes (valeur + timestamp), les plus récentes en premier
$res = $conn->query("SELECT valeur, timestamp FROM mesures ORDER BY timestamp DESC LIMIT 20");

// Initialisation d’un tableau pour stocker les résultats
$rows = [];

// Si des résultats sont trouvés, on les parcourt un à un et on les ajoute dans le tableau
if ($res && $res->num_rows > 0) {
while ($row = $res->fetch_assoc()) {
$rows[] = $row;
}
}

// On inverse le tableau pour que les données soient dans l’ordre chronologique (de l’ancienne à la plus récente)
echo json_encode(array_reverse($rows));

// Fermeture de la connexion à la base
$conn->close();
?>