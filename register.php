<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inscription - ClashDeck</title>
    <meta name="description" content="Inscrivez-vous gratuitement sur ClashDeck pour créer et gérer vos decks Clash Royale facilement.">
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

        require_once "model/User.php";
        require_once "model/UserController.php";

        $userController = new UserController();

        $error = "";
        $success = "";

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $firstName = trim($_POST["firstName"] ?? "");
            $lastName = trim($_POST["lastName"] ?? "");
            $email = trim($_POST["email"] ?? "");
            $password = $_POST["password"] ?? "";
            $confirmPassword = $_POST["confirm-password"] ?? "";

            // Vérification des champs
            if ($password !== $confirmPassword) {
                $error = "Les mots de passe ne correspondent pas.";
            } elseif ($userController->getUserByEmail($email)) {
                $error = "Un compte existe déjà avec cet e-mail.";
            } else {
                // Hash du mot de passe
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Création de l'utilisateur
                $newUser = new User([
                    "firstName" => $firstName,
                    "lastName" => $lastName,
                    "email" => $email,
                    "password" => $hashedPassword
                ]);

                $userController->createUser($newUser);

                // Stockage dans la session
                $_SESSION["firstName"] = $newUser->getFirstName();
                $_SESSION["lastName"] = $newUser->getLastName();
                $_SESSION["email"] = $newUser->getEmail();

                $success = "Inscription réussie ! Redirection en cours...";
                header("Refresh: 2; url=login.php"); // Rediriger après 2 secondes
                exit;
            }
        }
    ?>
    <div class="login-page">
        <a href="index.php" class="back-button">← Retour</a>
        <div class="login-container">
            <h1>Inscription</h1>
            <p>Veuillez entrer vos informations pour vous inscrire.</p>
            <form method="post" class="container mt-2">
                <input type="text" class="form-control" name="firstName" id="firstName" placeholder="Votre prénom" required min=2 maxlength=30>
                <input type="text" class="form-control" name="lastName" id="lastName" placeholder="Votre nom de famille" required minlength=2 maxlength=30>
                <input type="email" class="form-control" name="email" id="email" placeholder="Votre adresse e-mail" required>
                <input type="password" class="form-control" name="password" id="password" placeholder="Votre mot de passe" required>
                <input type="password" class="form-control" name="confirm-password" id="confirm-password" placeholder="Confirmez votre mot de passe" required>
                <button type="submit" class="mt-2" value="S'inscrire">S'inscrire</button>
            </form>
            <p>Déjà inscrit ? <a href="login.php">Connectez-vous ici</a></p>
        </div>
    </div>
</body>
</html>