<?php
session_start();
require_once 'config.php';
require_once 'model/DeckController.php';
require_once 'model/CommunityController.php';
require 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$view = $_GET['view'] ?? 'decks';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'desc';

$deckController = new DeckController($pdo);
$communityController = new CommunityController($pdo);

if (isset($_GET['delete_deck']) && is_numeric($_GET['delete_deck'])) {
    $deckId = (int)$_GET['delete_deck'];
    $deckController->deleteDeck($deckId, $_SESSION['user_id']);
    header('Location: my_posts.php?view=decks');
    exit;
}

if (isset($_GET['delete_comment']) && is_numeric($_GET['delete_comment'])) {
    $commentId = (int)$_GET['delete_comment'];
    $communityController->deleteComment($commentId, $_SESSION['user_id']);
    header('Location: my_posts.php?view=comments');
    exit;
}

$editCommentId = $_GET['edit_comment'] ?? null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment_id'], $_POST['message_edit'])) {
    $editId = (int)$_POST['edit_comment_id'];
    $newMessage = trim($_POST['message_edit']);
    if ($newMessage !== '') {
        $communityController->updateComment($editId, $newMessage, $_SESSION['user_id']);
        header('Location: my_posts.php?view=comments');
        exit;
    }
}

if ($view === 'decks') {
    $decks = $deckController->getDecksByUserId($_SESSION['user_id'], $search, $sort);
} else {
    $comments = $communityController->getCommentsByUserId($_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mes publications - ClashDeck</title>
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

<h1>Mes publications</h1>

<div class="tabs">
    <a href="my_posts.php?view=decks" class="<?= $view === 'decks' ? 'active' : '' ?>">Decks</a>
    <a href="my_posts.php?view=comments" class="<?= $view === 'comments' ? 'active' : '' ?>">Commentaires</a>
</div>

<?php if ($view === 'decks'): ?>

    <form method="get" action="my_posts.php" class="deck-search-form">
        <input type="hidden" name="view" value="decks">
        <input type="text" name="search" placeholder="Rechercher un deck..." value="<?= htmlspecialchars($search) ?>">
        <select name="sort">
            <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Plus récents</option>
            <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>Plus anciens</option>
        </select>
        <button type="submit">Filtrer</button>
    </form>

    <?php if (empty($decks)): ?>
        <p>Vous n'avez publié aucun deck.</p>
    <?php else: ?>
        <?php foreach ($decks as $deck): ?>
            <div class="deck">
                <h3><?= htmlspecialchars($deck->getTitle()) ?></h3>
                <p><?= nl2br(htmlspecialchars($deck->getDescription())) ?></p>
                <small>Ajouté le <?= htmlspecialchars($deck->getCreatedAt()) ?></small>

                <h4>Cartes :</h4>
                <div class="card-list">
                    <?php
                    $cards = $deckController->getCardsByDeckId($deck->getId());
                    foreach ($cards as $card): ?>
                        <div class="card">
                            <img src="<?= htmlspecialchars($card['image_url']) ?>"
                                alt="<?= htmlspecialchars($card['name']) ?>"
                                loading="lazy" width="100" height="120">
                            <div class="card-name"><?= htmlspecialchars($card['name']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div></br>
                <div class="actions">
                    <a href="edit_deck.php?id=<?= $deck->getId() ?>">Modifier</a> |
                    <a href="my_posts.php?view=decks&delete_deck=<?= $deck->getId() ?>" onclick="return confirm('Supprimer ce deck ?')">Supprimer</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

<?php else: ?>

    <?php if (empty($comments)): ?>
        <p>Vous n'avez publié aucun commentaire.</p>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <div class="message-card">
                <?php if ($editCommentId == $comment['id']): ?>
                    <form action="my_posts.php?view=comments" method="post">
                        <textarea name="message_edit" rows="4" required><?= htmlspecialchars($comment['message']) ?></textarea>
                        <input type="hidden" name="edit_comment_id" value="<?= $comment['id'] ?>">
                        <button type="submit">Sauvegarder</button>
                        <a href="my_posts.php?view=comments" class="btn-cancel">Annuler</a>
                    </form>
                <?php else: ?>
                    <p><strong><?= htmlspecialchars($_SESSION['firstName']) ?> <?= htmlspecialchars($_SESSION['lastName']) ?> :</strong></p>
                    <p><?= nl2br(htmlspecialchars($comment['message'])) ?></p>
                    <small>Posté le <?= $comment['created_at'] ?></small>
                    <div class="actions">
                        <a href="my_posts.php?view=comments&edit_comment=<?= $comment['id'] ?>">Modifier</a> |
                        <a href="my_posts.php?view=comments&delete_comment=<?= $comment['id'] ?>" onclick="return confirm('Supprimer ce commentaire ?')">🗑️ Supprimer</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

<?php endif; ?>

<script src="autoAdjustTexteArea.js" defer></script>
</body>
</html>
