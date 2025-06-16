<?php
require_once 'model/DeckController.php';
require_once 'model/Deck.php';

$pdo = new PDO('mysql:host=localhost;dbname=clash_deck;charset=utf8', 'secure_user', 'he93ts28&*');
$controller = new DeckController($pdo);
$json = file_get_contents('data/decks_100.json');
$data = json_decode($json, true);
$id = 0;


if ($data) { // Vérifie si le JSON a été chargé correctement
    $stmt1 = $pdo->prepare("INSERT INTO decks (title, description) VALUES (?, ?)"); // Prépare la requête d'insertion
    $stmt2 = $pdo->prepare("INSERT INTO deck_cards (deck_id,card_id) VALUES (?, ?)"); // Prépare la requête d'insertion
    foreach ($data as $decks) {
        $deck = new Deck([
            'title' => $decks['name'],
            'description' => $decks['commentaire']
        ]);
        $controller->addDeck($deck);

        // Récupération de l'ID du nouveau deck
        $deckId = $pdo->lastInsertId();

        // Insertion des cartes sélectionnées dans la table intermédiaire
        foreach ($decks['cards'] as $cardId) {
            $stmt2->execute([$deckId, $cardId]);
        }
    }
    echo "Deck insérées avec succès !";
} else {
    echo "Erreur lors du chargement du fichier JSON.";
}
?>