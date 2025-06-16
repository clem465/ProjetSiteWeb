<?php
require_once 'config.php'; // Inclut le fichier de configuration pour la connexion à la base de données
require_once 'model/DeckController.php'; // Inclut le contrôleur des decks
session_start(); // Démarre la session pour accéder aux variables de session

if (!isset($_SESSION['user_id'], $_GET['deck_id'])) { // Vérifie si l'utilisateur est connecté et si l'ID du deck est fourni
    header("Location: index.php"); // Redirige vers la page d'accueil si non connecté ou ID de deck manquant
    exit;
}

$userId = $_SESSION['user_id']; // Récupère l'ID de l'utilisateur depuis la session
$deckId = (int) $_GET['deck_id'];

$controller = new DeckController($pdo);

// Ajoute ou enlève des favoris
if ($controller->isFavorite($userId, $deckId)) { // Vérifie si le deck est déjà dans les favoris de l'utilisateur
    $controller->removeFavorite($userId, $deckId); // Si oui, on le retire des favoris
} else {
    $controller->addFavorite($userId, $deckId); // Sinon, on l'ajoute aux favoris
}

// Redirection vers la page précédente (ou vers index par défaut si non définie)
$redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php'; // Récupère l'URL de la page précédente ou redirige vers index.php si non définie
header("Location: $redirect"); // Redirige vers la page précédente
exit;