<?php
session_start();
require 'header.php'; // Assurez-vous que le header.php contient les sessions et la connexion à la base de données
require 'config.php'; // Inclure le fichier de configuration pour la connexion à la base de données

// Vérifier que l'utilisateur est connecté et id deck passé
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) { // Vérifier si l'utilisateur est connecté et si l'ID du deck est passé
    header('Location: login.php'); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté ou si l'ID du deck n'est pas passé
    exit; // Terminer le script pour éviter toute exécution supplémentaire
}

$deckId = (int) $_GET['id']; // Convertir l'ID du deck en entier pour éviter les injections SQL
$userId = $_SESSION['user_id']; // Récupérer l'ID de l'utilisateur connecté depuis la session

// Récupérer le deck à modifier (uniquement si c'est bien son deck)
$stmt = $pdo->prepare("SELECT * FROM decks WHERE id = ? AND author_id = ?"); // Préparer la requête pour récupérer le deck
$stmt->execute([$deckId, $userId]); // Exécuter la requête avec l'ID du deck et l'ID de l'utilisateur
$deck = $stmt->fetch(PDO::FETCH_ASSOC); // Récupérer le deck en tant que tableau associatif

if (!$deck) {
    die("Deck non trouvé ou accès refusé."); // Si le deck n'existe pas ou si l'utilisateur n'est pas l'auteur, afficher un message d'erreur
}

// Récupérer les cartes du deck (ids)
$stmt = $pdo->prepare("SELECT card_id FROM deck_cards WHERE deck_id = ?"); // Préparer la requête pour récupérer les cartes du deck
$stmt->execute([$deckId]); // Exécuter la requête avec l'ID du deck
$deckCards = $stmt->fetchAll(PDO::FETCH_COLUMN); // Récupérer les IDs des cartes du deck en tant que tableau

// Récupérer toutes les cartes pour affichage
$stmt = $pdo->query("SELECT id, name, image_url FROM cards"); // Préparer la requête pour récupérer toutes les cartes
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer toutes les cartes en tant que tableau associatif

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Vérifier si la méthode de la requête est POST
    $title = trim($_POST['title'] ?? ''); // Récupérer le titre du deck depuis le formulaire et le nettoyer
    $description = trim($_POST['description'] ?? ''); // Récupérer la description du deck depuis le formulaire et la nettoyer
    $selectedCards = $_POST['cards'] ?? []; // Récupérer les cartes sélectionnées depuis le formulaire, ou un tableau vide si aucune carte n'est sélectionnée

    // Validation basique
    if ($title === '' || $description === '') { // Vérifier si le titre et la description sont remplis
        $error = "Le titre et la description sont obligatoires."; // Si l'un des champs est vide, définir un message d'erreur
    } elseif (count($selectedCards) > 8) { // Vérifier si le nombre de cartes sélectionnées dépasse 8
        $error = "Vous ne pouvez sélectionner que jusqu'à 8 cartes."; // Si plus de 8 cartes sont sélectionnées, définir un message d'erreur
    } else {
        // Mise à jour du deck
        $stmt = $pdo->prepare("UPDATE decks SET title = ?, description = ? WHERE id = ? AND author_id = ?"); // Préparer la requête pour mettre à jour le deck
        $stmt->execute([$title, $description, $deckId, $userId]); // Exécuter la requête avec le titre, la description, l'ID du deck et l'ID de l'utilisateur

        // Mise à jour des cartes : on supprime d'abord les anciennes
        $stmt = $pdo->prepare("DELETE FROM deck_cards WHERE deck_id = ?"); // Préparer la requête pour supprimer les cartes du deck
        $stmt->execute([$deckId]); // Exécuter la requête avec l'ID du deck pour supprimer les cartes existantes

        // On insère les nouvelles cartes sélectionnées
        $stmt = $pdo->prepare("INSERT INTO deck_cards (deck_id, card_id) VALUES (?, ?)"); // Préparer la requête pour insérer les nouvelles cartes dans le deck
        foreach ($selectedCards as $cardId) { // Pour chaque carte sélectionnée
            $stmt->execute([$deckId, (int)$cardId]); // Exécuter la requête avec l'ID du deck et l'ID de la carte
        }

        // Redirection vers mes publications
        header('Location: my_posts.php'); //   Rediriger vers la page des publications de l'utilisateur
        exit; // Terminer le script pour éviter toute exécution supplémentaire
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modifier le deck</title>
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

<h1>Modifier le deck</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" aria-label="Formulaire de modification de deck">
    <label for="title">Titre du deck :</label><br />
    <input
        type="text"
        id="title"
        name="title"
        required
        maxlength="100"
        value="<?= htmlspecialchars($_POST['title'] ?? $deck['title']) ?>"
        aria-required="true"
    /><br/><br/>

    <label for="description">Description :</label><br />
    <textarea
        id="description"
        name="description"
        required
        rows="4"
        maxlength="500"
        aria-required="true"
    ><?= htmlspecialchars($_POST['description'] ?? $deck['description']) ?></textarea><br/><br/>

    <button type="submit">Enregistrer les modifications</button>

    <div class="cards">
        <label>Choisir jusqu’à 8 cartes :</label><br />
        <div style="display: flex; flex-wrap: wrap; justify-content: center;">
            <?php foreach ($cards as $card): ?>
                <label style="margin: 5px; text-align: center; cursor: pointer;">
                    <img
                        src="<?= htmlspecialchars($card['image_url'] ?? 'default-image-url.jpg') ?>"
                        alt="<?= htmlspecialchars($card['name'] ?? 'Carte inconnue') ?>"
                        width="175"
                        height="auto"
                        loading="lazy"
                    />
                    <br />
                    <input
                        type="checkbox"
                        name="cards[]"
                        value="<?= htmlspecialchars($card['id']) ?>"
                        onchange="checkLimit(this)"
                        aria-label="Sélectionner la carte <?= htmlspecialchars($card['name']) ?>"
                        <?= (in_array($card['id'], $_POST['cards'] ?? $deckCards)) ? 'checked' : '' ?>
                    />
                    <?= htmlspecialchars($card['name']) ?>
                </label>
            <?php endforeach; ?>
        </div><br />
    </div>

    
</form>

<script>
function checkLimit(checkbox) {
    const max = 8;
    const checkedBoxes = document.querySelectorAll('input[name="cards[]"]:checked');
    if (checkedBoxes.length > max) {
        checkbox.checked = false;
        alert(`Vous ne pouvez sélectionner que jusqu'à ${max} cartes.`);
    }
}
</script>

</body>
</html>
