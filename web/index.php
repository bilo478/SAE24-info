<?php
require_once("db_config.php");
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) exit("Connexion échouée : " . $conn->connect_error);


$result = $conn->query("SELECT * FROM mesures ORDER BY timestamp DESC LIMIT 1");
$data = $result->fetch_assoc();
// Récupération du nombre total de "presence"
$total = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM mesures WHERE LOWER(valeur) = 'presence'");
if ($res) {
    $row = $res->fetch_assoc();
    $total = $row['total'];
}
$conn->close();

$valeur = $data ? strtolower($data['valeur']) : 'aucune';
$timestamp = $data ? $data['timestamp'] : null;

$message = ($valeur === 'presence') ? "Présence détectée" : "Aucune présence détectée";
$classeCouleur = ($valeur === 'presence') ? "presence" : "absence";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Visualisation - SAE24</title>
  <meta http-equiv="refresh" content="5">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Suivi des présences - SAE24</h1>

    <div class="carte <?= $classeCouleur ?>">
      <h2><?= $message ?></h2>
      <p class="total">Présence(s) totale(s) détectée(s) : <strong><?= $total ?></strong></p>
      <?php if ($timestamp): ?>
        <?php
          $date = new DateTime($timestamp);
          $now = new DateTime();

          $diff = $now->diff($date)->days;

          if ($date->format('Y-m-d') === $now->format('Y-m-d')) {
            $affichage = "Aujourd’hui à " . $date->format("H:i:s");
          } elseif ($diff === 1) {
            $affichage = "Hier à " . $date->format("H:i:s");
          } else {
            $affichage = $date->format("d/m/Y à H:i:s");
          }
        ?>
        <p class="timestamp">Dernière détection :<br><strong><?= $affichage ?></strong></p>
      <?php else: ?>
        <p class="timestamp">Aucune détection encore enregistrée.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>