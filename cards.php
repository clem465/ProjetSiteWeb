<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cartes Clash Royale</title>

    <meta name="description" content="Explorez toutes les cartes Clash Royale avec tri et recherche par nom, coût d'élixir et rareté." />

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
    require_once 'tri.php';
    ?>

    <main id="main-content" tabindex="-1">
        <h1>Toutes les cartes Clash Royale</h1>

        <form method="get" aria-label="Filtrer les cartes">
            <label for="search">Recherche par nom :</label>
            <input
                type="text"
                name="search"
                id="search"
                placeholder="Recherche par nom..."
                value="<?= htmlspecialchars($search ?? '') ?>"
            />

            <label for="sort">Trier par :</label>
            <select name="sort" id="sort" aria-describedby="sort-description">
                <option value="">-- Trier par --</option>
                <option value="name" <?= ($sort ?? '') === 'name' ? 'selected' : '' ?>>Nom</option>
                <option value="elixir" <?= ($sort ?? '') === 'elixir' ? 'selected' : '' ?>>Coût d'élixir</option>
                <option value="rarity" <?= ($sort ?? '') === 'rarity' ? 'selected' : '' ?>>Rareté</option>
            </select>
            <span id="sort-description" class="visually-hidden">Trier les cartes affichées</span>

            <button type="submit">Filtrer</button>
        </form>

        <section class="card-container" aria-label="Cartes Support">
            <h2>Cartes Support</h2>
            <div class="cards-wrapper" style="display: flex; flex-wrap: wrap; justify-content: center;">
                <?php foreach ($cards2 ?? [] as $card2): ?>
                    <article class="card" role="listitem">
                        <img
                            src="<?= htmlspecialchars($card2['iconUrls']['medium'] ?? 'default-image-url.jpg') ?>"
                            alt="<?= htmlspecialchars($card2['name'] ?? 'Carte inconnue') ?>"
                            loading="lazy"
                            decoding="async"
                            width="80"
                            height="80"
                        />
                        <p><?= htmlspecialchars($card2['name'] ?? 'Carte inconnue') ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="card-container" aria-label="Cartes du Jeu">
            <h2>Cartes du Jeu</h2>
            <div class="cards-wrapper" style="display: flex; flex-wrap: wrap; justify-content: center;">
                <?php foreach ($cards ?? [] as $card): ?>
                    <article class="card" role="listitem">
                        <img
                            src="<?= htmlspecialchars($card['iconUrls']['medium'] ?? 'default-image-url.jpg') ?>"
                            alt="<?= htmlspecialchars($card['name'] ?? 'Carte inconnue') ?>"
                            loading="lazy"
                            decoding="async"
                            width="80"
                            height="80"
                        />
                        <p><?= htmlspecialchars($card['name'] ?? 'Carte inconnue') ?></p>
                        <p>Coût : <?= htmlspecialchars($card['elixirCost'] ?? 'N/A') ?></p>
                        <p>Rareté : <?= htmlspecialchars($card['rarity'] ?? 'N/A') ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>
</html>
