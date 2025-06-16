<?php
session_start(); // Démarre la session pour accéder aux variables de session
require_once 'model/NewsController.php'; // Inclut le contrôleur des actualités

$controller = new NewsController(); // Crée une instance du contrôleur des actualités
$articles = $controller->getArticles(); // Récupère les articles via le contrôleur
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Actualités - ClashDeck</title>
    <meta name="description" content="Créez facilement votre deck Clash Royale personnalisé avec ClashDeck. Sélectionnez jusqu’à 8 cartes parmi une large sélection." />
    <link rel="stylesheet" href="style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Bangers&display=swap" rel="stylesheet">
    </noscript>
    <link rel="shortcut icon" href="pict/goblin.webp" />
</head>
<body>
    <?php require 'header.php'; ?>

    <h1>📰 Actualités Clash Royale</h1><br>

    <div class="news-container">
        <?php if (!empty($articles)): ?> 
            <?php foreach ($articles as $article): ?>
                <a href="<?= htmlspecialchars($article['lien']) ?>" target="_blank" class="article" rel="noopener noreferrer">
                    <img src="<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>">
                    <div class="article-content">
                        <h2><?= htmlspecialchars($article['titre']) ?></h2>
                        <p><strong>Date :</strong> <?= htmlspecialchars($article['date']) ?></p>
                        <p><strong>Catégorie :</strong> <?= htmlspecialchars($article['categorie']) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun article trouvé.</p>
        <?php endif; ?>
    </div>
</body>
</html>
