<?php
require_once 'model/DeckController.php';
require_once 'model/Deck.php';

// Assure-toi que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=clash_deck;charset=utf8', 'secure_user', 'he93ts28&*');
$controller = new DeckController($pdo);

// Chargement des cartes depuis le fichier JSON local
$cards = [];
$json = file_get_contents('data/cards.json');
if ($json !== false) {
    $data = json_decode($json, true);
    $cards = $data['items'] ?? [];
} else {
    die("Impossible de lire les cartes depuis le fichier local.");
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $cardIds = $_POST['cards'] ?? [];

    if ($title && $description && count($cardIds) > 0 && count($cardIds) <= 8) {
        // ✅ On transmet aussi author_id ici
        $deck = new Deck([
            'title' => $title,
            'description' => $description,
            'author_id' => $userId
        ]);

        // Ajout dans la base
        $controller->addDeck($deck);
        $deckId = $pdo->lastInsertId();

        // Cartes du deck
        $stmt = $pdo->prepare("INSERT INTO deck_cards (deck_id, card_id) VALUES (?, ?)");
        foreach ($cardIds as $cardId) {
            $stmt->execute([$deckId, $cardId]);
        }

        // Redirection
        header('Location: index.php');
        exit;
    } else {
        echo "<p style='color:red;'>Veuillez remplir tous les champs et choisir entre 1 et 8 cartes.</p>";
    }
}
?>
