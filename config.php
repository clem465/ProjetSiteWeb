<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=clash_deck;charset=utf8', 'secure_user', 'he93ts28&*'); // Connexion à la base de données
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Affiche les erreurs PDO
} catch (PDOException $e) { // Si la connexion échoue, on affiche un message d'erreur
    die("Erreur de connexion : " . $e->getMessage());
}
