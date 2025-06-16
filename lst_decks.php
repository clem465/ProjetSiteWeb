<?php
require_once 'config.php';
require_once 'model/DeckController.php';

$search = $_GET['search'] ?? ''; // Récupère le terme de recherche depuis les paramètres GET, ou une chaîne vide si non défini
$sort = $_GET['sort'] ?? 'recent'; // Récupère le critère de tri depuis les paramètres GET, ou 'recent' par défaut
$page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) ? (int)$_GET['page'] : 1; // Récupère le numéro de page depuis les paramètres GET, ou 1 par défaut
$perPage = 10; // Nombre de decks à afficher par page
$offset = ($page - 1) * $perPage; // Calcul de l'offset pour la requête SQL

$controller = new DeckController($pdo); // Crée une instance du contrôleur des decks
$decks = $controller->getFilteredAndSortedDecks($search, $sort, $perPage, $offset); // Récupère les decks filtrés et triés selon les critères de recherche et de tri, avec pagination
$totalDecks = $controller->countFilteredDecks($search); // Compte le nombre total de decks correspondant aux critères de recherche pour la pagination
$totalPages = ceil($totalDecks / $perPage); // Calcule le nombre total de pages nécessaires pour afficher tous les decks
?>

<main>
    <h1>Liste des decks Clash Royale</h1>

    <form method="get" class="deck-filter-form" style="text-align:center; margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Rechercher un deck..." value="<?= htmlspecialchars($search) ?>">
        <select name="sort">
            <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Les plus récents</option>
            <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Les plus anciens</option>
        </select>
        <button type="submit">Filtrer</button>
    </form>

    <div class="deck-container">
        <?php if (empty($decks)): ?>
            <p style="text-align: center;">Aucun deck ne correspond à votre recherche.</p>
        <?php endif; ?>

        <?php foreach ($decks as $deck): ?>
            <article class="deck">
                <h2><?= htmlspecialchars($deck->getTitle()) ?></h2>

                <?php if (is_connected()): ?>
                    <?php
                        $userId = (int) $_SESSION['user_id'];
                        $deckId = $deck->getId();
                        $isFavorite = $controller->isFavorite($userId, $deckId);
                    ?>
                    <form action="favorites.php" method="post" class="favorite-form">
                        <input type="hidden" name="deck_id" value="<?= $deckId ?>">
                        <input type="hidden" name="action" value="<?= $isFavorite ? 'remove' : 'add' ?>">
                        <button type="submit" class="favorite-button"
                                aria-label="<?= $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>">
                            <?= $isFavorite
                                ? '<img src="pict/icons8-favoris-50 (3).webp" alt="Favori" loading="lazy">'
                                : '<img src="pict/icons8-favoris-50.webp" alt="Pas favori" loading="lazy">' ?>
                        </button>
                    </form>
                <?php endif; ?>

                <p><?= nl2br(htmlspecialchars($deck->getDescription())) ?></p>
                <small>Ajouté le <?= $deck->getCreatedAt() ?></small>

                <h4>Cartes :</h4>
                <div class="card-list">
                    <?php
                    $cards = $controller->getCardsByDeckId($deck->getId());
                    foreach ($cards as $card): ?>
                        <div class="card">
                            <img src="<?= htmlspecialchars($card['image_url']) ?>"
                                 alt="<?= htmlspecialchars($card['name']) ?>"
                                 loading="lazy" width="100" height="120">
                            <div class="card-name"><?= htmlspecialchars($card['name']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php require 'pagination.php'; ?>
</main>
