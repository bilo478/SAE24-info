<?php
if (isset($_POST['donnee'])) {
    $donnee = $_POST['donnee'];
    $timestamp = date("Y-m-d H:i:s");

    $fichier = fopen("log.txt", "a");
    fwrite($fichier, $timestamp . " - " . $donnee . "\n");
    fclose($fichier);

    require_once("db_config.php");
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) exit();

    $stmt = $conn->prepare("INSERT INTO mesures (timestamp, valeur) VALUES (?, ?)");
    $stmt->bind_param("ss", $timestamp, $donnee);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    echo "OK";
} else {
    echo "Erreur";
}
?>

