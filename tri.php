<?php
// Récupérer le fichier JSON des cartes
$jsonFilePath = 'data/cards.json';
$cards = [];
$cards2 = [];

if (file_exists($jsonFilePath)) {
    $json = file_get_contents($jsonFilePath);
    $data = json_decode($json, true);
    $cards = $data['items'] ?? [];
    $cards2 = $data['supportItems'] ?? [];
} else {
    die("Erreur : Le fichier JSON des cartes est introuvable.");
}

// Gérer les paramètres de recherche et de tri
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';

// Filtrage par recherche
if (!empty($search)) {
    $cards = array_filter($cards, function ($card) use ($search) {
        return stripos($card['name'], $search) !== false;
    });
    $cards2 = array_filter($cards2, function ($card) use ($search) {
        return stripos($card['name'], $search) !== false;
    });
}

// Tri des cartes
if ($sort === 'name') {
    usort($cards, fn($a, $b) => strcmp($a['name'], $b['name']));
    usort($cards2, fn($a, $b) => strcmp($a['name'], $b['name']));
} elseif ($sort === 'elixir') {
    usort($cards, fn($a, $b) => ($a['elixirCost'] ?? 0) <=> ($b['elixirCost'] ?? 0));
    usort($cards2, fn($a, $b) => ($a['elixirCost'] ?? 0) <=> ($b['elixirCost'] ?? 0));
} elseif ($sort === 'rarity') {
    $rarityOrder = ['common' => 1, 'rare' => 2, 'epic' => 3, 'legendary' => 4, 'champion' => 5];
    usort($cards, fn($a, $b) => ($rarityOrder[$a['rarity']] ?? 0) <=> ($rarityOrder[$b['rarity']] ?? 0));
    usort($cards2, fn($a, $b) => ($rarityOrder[$a['rarity']] ?? 0) <=> ($rarityOrder[$b['rarity']] ?? 0));
}
?>
