<?php
session_start();  // Bien démarrer la session

require_once 'config.php'; // Ta connexion PDO dans $pdo

// Vérifier que l'utilisateur est connecté avant d'insérer ou supprimer
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Insertion d'un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    $stmt = $pdo->prepare("INSERT INTO community_messages (user_id, message, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$_SESSION['user_id'], $message]);
}

// Suppression d'un message par son auteur
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $msgId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM community_messages WHERE id = ? AND user_id = ?");
    $stmt->execute([$msgId, $_SESSION['user_id']]);
}

// Récupération des messages
$stmt = $pdo->query("
    SELECT m.id, m.message, m.created_at, u.firstName, u.lastName, m.user_id
    FROM community_messages m
    JOIN users u ON m.user_id = u.id
    ORDER BY m.created_at DESC
");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Communauté - ClashDeck</title>
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

    <div class="container">
        <h1>💬 Communauté ClashDeck</h1>

        <form action="" method="post" class="message-form">
            <textarea name="message" rows="4" placeholder="Écris ton message ici..." required></textarea>
            <button type="submit">Envoyer</button>
        </form>

        <div class="messages">
            <?php foreach ($messages as $msg): ?>
                <div class="message-card">
                    <p><strong><?= htmlspecialchars($msg['firstName']) ?> <?= htmlspecialchars($msg['lastName']) ?> :</strong></p>
                    <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                    <small>Posté le <?= $msg['created_at'] ?></small>
                    <?php if ($_SESSION['user_id'] === $msg['user_id']): ?>
                        <a href="?delete=<?= $msg['id'] ?>" onclick="return confirm('Supprimer ce message ?')">🗑️ Supprimer</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
