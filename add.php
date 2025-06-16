<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Créer un deck Clash Royale - ClashDeck</title>
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
<?php
session_start();
require 'header.php';
require 'cardRecovery.php';

if (!is_connected()) {
    header('Location: login.php');
    exit;
}
?>

<main>
    <h1>Créer un deck Clash Royale</h1>

    <form method="post" aria-label="Formulaire de création de deck">
        <section class="title">
            <label for="title">Titre du deck :</label><br />
            <input type="text" id="title" name="title" required maxlength="100" aria-required="true" /><br /><br />
        </section>

        <section class="description">
            <label for="description">Description :</label><br /> 
            <textarea id="description" name="description" required aria-required="true" rows="4" maxlength="500"></textarea><br /><br />
        </section>

        <button type="submit">Créer le deck</button>

        <fieldset class="cards" aria-labelledby="cardsLegend">
            <legend id="cardsLegend">Choisir jusqu’à 8 cartes :</legend>
            <div style="display: flex; flex-wrap: wrap; justify-content: center;">
                <?php foreach ($cards as $card):  // Assuming $cards is an array of card data
                    // Ensure card data is sanitized for HTML output
                    $cardId = htmlspecialchars($card['id'] ?? ''); // Use a default value if 'id' is not set
                    $cardName = htmlspecialchars($card['name'] ?? 'Carte inconnue'); // Use a default value if 'name' is not set
                    $cardImg = htmlspecialchars($card['iconUrls']['medium'] ?? 'default-image-url.jpg'); // Use a default image URL if 'iconUrls' or 'medium' is not set
                ?>
                    <label style="margin: 5px; text-align: center; cursor: pointer;">
                        <img 
                            src="<?= $cardImg ?>" 
                            alt="<?= $cardName ?>" 
                            width="150" height="220"
                            loading="lazy" />
                        <br />
                        <input 
                            type="checkbox" 
                            name="cards[]" 
                            value="<?= $cardId ?>" 
                            onchange="checkLimit(this)" 
                            aria-label="Sélectionner la carte <?= $cardName ?>" />
                        <?= $cardName ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </fieldset><br/>
    </form>
</main>

<script src="checkLimit.js" defer></script>
</body>
</html>
