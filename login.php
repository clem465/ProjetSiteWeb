<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion - ClashDeck</title>
    <meta name="description" content="Page de connexion sécurisée pour accéder à ClashDeck, votre gestionnaire de decks Clash Royale." />
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
require_once "model/UserController.php";
require_once "model/User.php";

$userController = new UserController();

$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"] ?? '';

    if (!$email) {
        $error = "Veuillez saisir un email valide.";
    } elseif (empty($password)) {
        $error = "Le mot de passe est requis.";
    } else {
        $user = $userController->getUserByEmail($email);
        if ($user && password_verify($password, $user->getPassword())) {
            // Connexion réussie
            $_SESSION["user_id"] = $user->getId();
            $_SESSION["firstName"] = $user->getFirstName();
            $_SESSION["lastName"] = $user->getLastName();
            $_SESSION["email"] = $user->getEmail();
            $_SESSION["user"] = $user;

            header("Location: index.php");
            exit;
        } else {
            $error = "Identifiants invalides.";
        }
    }
}
?>

<main class="login-page" role="main" id="main-content" tabindex="-1">
    <a href="index.php" class="back-button" aria-label="Retour à l'accueil">← Retour</a>
    <section class="login-container" aria-labelledby="login-title">
        <h1 id="login-title">Connexion</h1>
        <p>Veuillez entrer vos identifiants pour vous connecter.</p>

        <?php if ($error): ?>
            <div role="alert" class="error-message" style="color:red; font-weight:bold;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="container mt-1" novalidate>
            <label for="email">Adresse email</label><br />
            <input
                type="email"
                name="email"
                id="email"
                required
                placeholder="Email"
                value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                autocomplete="username"
                aria-describedby="emailHelp"
            />
            <small id="emailHelp" class="visually-hidden">Entrez votre adresse email enregistrée.</small><br />

            <label for="password">Mot de passe</label><br />
            <input
                type="password"
                name="password"
                id="password"
                required
                placeholder="Mot de passe"
                autocomplete="current-password"
            /><br />

            <button type="submit">Connexion</button>
        </form>
        <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
    </section>
</main>

</body>
</html>
