<?php
require("index.php"); // Redirection vers la page d'accueil

session_destroy(); // Détruit la session pour déconnecter l'utilisateur
$_SESSION = []; // Réinitialise les variables de session
echo "<script>window.location.href='index.php'</script>"; // Redirige vers la page d'accueil