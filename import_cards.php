<?php
$pdo = new PDO('mysql:host=localhost;dbname=clash_deck;charset=utf8', 'secure_user', 'he93ts28&*'); // Connexion à la base de données
$json = file_get_contents('data/cards.json'); // Charge le fichier JSON contenant les cartes
$data = json_decode($json, true); // Décode le JSON en tableau associatif

if ($data && isset($data['items'])) { // Vérifie si le JSON a été chargé correctement
    $stmt = $pdo->prepare("INSERT INTO cards (id, name, elixir, rarity, image_url) VALUES (?, ?, ?, ?, ?)"); // Prépare la requête d'insertion
    foreach ($data['items'] as $card) { // Parcourt chaque carte dans le tableau
        $stmt->execute([ // Exécute la requête d'insertion avec les données de la carte
            $card['id'],
            $card['name'],
            $card['elixirCost'] ?? null, // Vérifie si le coût en élixir est défini
            $card['rarity'],
            $card['iconUrls']['medium'] ?? null // Vérifie si l'URL de l'image est valide
        ]);
    }
    echo "Cartes insérées avec succès !";
} else {
    echo "Erreur lors du chargement du fichier JSON.";
}

?>