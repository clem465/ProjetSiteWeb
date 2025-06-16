<?php
require_once 'model/User.php';  // Le chemin vers la classe User
session_start(); // Démarre la session pour accéder aux variables de session
require_once 'config.php'; // Inclut le fichier de configuration pour la connexion à la base de données
require_once 'model/DeckController.php'; // Inclut le contrôleur des decks

$controller = new DeckController($pdo); // Crée une instance du contrôleur des decks

if (!isset($_SESSION['user']) || !isset($_POST['deck_id'])) { // Vérifie si l'utilisateur est connecté et si l'ID du deck est passé
    header('Location: index.php'); // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté ou si l'ID du deck n'est pas fourni
    exit; // Termine le script pour éviter toute exécution supplémentaire
}

$userId = $_SESSION['user']->getId(); // Récupère l'ID de l'utilisateur connecté depuis la session
$deckId = (int)$_POST['deck_id']; // Récupère l'ID du deck depuis le formulaire POST et le convertit en entier
$action = $_POST['action'] ?? ''; // Récupère l'action (ajout ou suppression) depuis le formulaire POST, avec une valeur par défaut vide

if ($action === 'remove') { // Si l'action est de supprimer le deck des favoris
    $controller->removeFavorite($userId, $deckId); // Appelle la méthode pour supprimer le deck des favoris de l'utilisateur
} else { // Sinon, on considère que l'action est d'ajouter le deck aux favoris
    $controller->addFavorite($userId, $deckId); // Appelle la méthode pour ajouter le deck aux favoris de l'utilisateur
}

header('Location: index.php'); // Redirige vers la page d'accueil après l'ajout ou la suppression du deck des favoris
exit; // Termine le script pour éviter toute exécution supplémentaire
?>