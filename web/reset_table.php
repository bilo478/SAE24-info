<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Accès refusé.");
}
require_once("db_config.php");

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    exit("Connexion échouée : " . $conn->connect_error);
}

$conn->query("DELETE FROM mesures");
$conn->close();

echo "Table vidée avec succès.";
?>