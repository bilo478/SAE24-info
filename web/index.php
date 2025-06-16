<?php
require_once("db_config.php");
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) exit();

$result = $conn->query("SELECT * FROM mesures");

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta http-equiv="refresh" content="5"><link rel="stylesheet" href="style.css"></head><body>';
echo '<h2>Données enregistrées</h2>';
echo '<table><tr><th>ID</th><th>Horodatage</th><th>Valeur</th></tr>';

while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['timestamp']}</td><td>{$row['valeur']}</td></tr>";
}

echo '</table></body></html>';
$conn->close();
?>
