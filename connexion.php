<?php
// includes/connexion.php — Connexion MySQL COCKTAIL'S
$host   = "localhost";
$dbname = "jadenbrival_tropik";
$user   = "jadenbrival_PDG";
$pass   = "ja_Y_den972+";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
