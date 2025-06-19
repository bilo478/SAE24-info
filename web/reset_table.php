<?php
// Vérifie que la requête est bien une requête POST pour éviter les accès directs ou non autorisés
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Accès refusé."); // Stoppe immédiatement si ce n'est pas une requête POST
}

// Inclusion des paramètres de connexion à la base de données (hôte, utilisateur, mot de passe, base, port)
require_once("db_config.php");

// Connexion à la base de données avec les constantes définies dans db_config.php
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Vérifie si la connexion a échoué et affiche un message explicite en cas d’erreur
if ($conn->connect_error) {
    exit("Connexion échouée : " . $conn->connect_error);
}

// Supprime toutes les données de la table "mesures" (vide la table)
$conn->query("DELETE FROM mesures");

// Ferme proprement la connexion à la base de données
$conn->close();

// Retourne un message confirmant que la suppression s’est bien déroulée
echo "Table vidée avec succès.";
?>
