<?php
function is_connected(): bool {
    return isset($_SESSION["email"], $_SESSION["firstName"], $_SESSION["lastName"], $_SESSION["user"], $_SESSION["user_id"]);
}
?>

<header>
    <?php if (is_connected()): ?>
        <p class="m-2">Bienvenue <?= htmlspecialchars($_SESSION["firstName"]) ?> <?= htmlspecialchars($_SESSION["lastName"]) ?> !</p>
    <?php endif; ?>

    <nav class="navbar" role="navigation" aria-label="Menu principal">
        <div class="nav-logo-container">
            <a href="index.php" aria-label="Retour à l'accueil">
                <img src="pict/King_blue_laughing_final.webp" id="king" alt="Logo ClashDeck : Roi Clash Royale riant" width="48" height="48" loading="lazy">
            </a>
            <span class="nav-logo">ClashDeck</span>
        </div>

        <ul class="nav-links">
            <li><a href="index.php">🏠️ Accueil</a></li>
            <li><a href="cards.php">🧾 Liste des cartes</a></li>
            <li><a href="add.php">➕ Ajouter un deck</a></li>
            <li><a href="news.php">📰 Actualités</a></li>
            <?php if (is_connected()): ?>
                <li><a href="myfavorite.php">⭐ Favoris</a></li>
                 <li><a href="community.php">💬 Communauté</a></li> <!-- ← ajout ici -->
            <?php endif; ?>
        </ul>

        <div class="nav-auth">
            <?php if (!is_connected()): ?>
                <a href="login.php" class="auth-link">Se connecter</a>
            <?php else: ?>
                <div class="dropdown">
                    <a href="#" class="auth-link dropbtn">Mon profil</a>
                    <ul class="dropdown-content" role="menu" aria-label="Sous-menu Mon profil">
                        <li><a href="my_posts.php" role="menuitem">Mes publications</a></li>
                        <li><a href="logout.php" role="menuitem">Se déconnecter</a></li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

            
        </div>
    </nav>
</header>
