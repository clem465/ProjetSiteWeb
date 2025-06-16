<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mes decks favoris - Clash Royale</title>
    <meta name="description" content="Liste de mes decks Clash Royale favoris, avec description et cartes associées." />
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
    require_once 'model/DeckController.php';

    if (!is_connected()) {
        header("Location: login.php");
        exit;
    }

    $controller = new DeckController($pdo);
    $decks = $controller->getFavoriteDecksByUserId($_SESSION['user_id']);
    ?>

    <main>
        <h1>Mes decks favoris</h1>

        <a href="index.php" id="back_button" aria-label="Retour à la liste des decks">← Retour à la liste</a>

        <section class="deck-container" role="list">
            <?php foreach ($decks as $deck): ?>
                <article class="deck" role="listitem">
                    <h2><?= htmlspecialchars($deck->getTitle()) ?></h2>
                    <p><?= nl2br(htmlspecialchars($deck->getDescription())) ?></p>
                    <small>Ajouté le <?= htmlspecialchars($deck->getCreatedAt()) ?></small>

                    <form action="toggle_favorite.php" method="get" aria-label="Retirer <?= htmlspecialchars($deck->getTitle()) ?> des favoris">
                        <input type="hidden" name="deck_id" value="<?= htmlspecialchars($deck->getId()) ?>" />
                        <button type="submit" aria-pressed="true" class="favorite-remove-btn">💔 Retirer des favoris</button>
                    </form>

                    <h3>Cartes :</h3>
                    <?php
                    $deckId = $deck->getId();
                    $cards = $controller->getCardsByDeckId($deckId);
                    ?>

                    <div class="card-list">
                        <?php foreach ($cards as $card): ?>
                            <div class="card">
                                <img src="<?= htmlspecialchars($card['image_url']) ?>" alt="Carte <?= htmlspecialchars($card['name']) ?>" loading="lazy" />
                                <div class="card-name"><?= htmlspecialchars($card['name']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
