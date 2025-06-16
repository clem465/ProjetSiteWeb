<!-- Pagination -->
<div class="pagination">
    <?php if ($totalPages > 1): ?>
        <?php
        $queryParams = $_GET; // Récupère les paramètres de requête actuels pour la pagination
        function pageLink($p) { // Fonction pour générer le lien de la page
            global $queryParams; // Accède aux paramètres de requête globaux
            $queryParams['page'] = $p; // Met à jour le numéro de page dans les paramètres de requête
            return '?' . http_build_query($queryParams); // Construit l'URL de la page avec les paramètres de requête
        }

        if ($page > 1) { // Vérifie si on n'est pas à la première page
            echo '<a href="' . pageLink($page - 1) . '">&laquo; Précédent</a> '; // Lien vers la page précédente
        }

        $range = 2; // Plage de pages à afficher autour de la page courante
        $start = max(1, $page - $range); // Détermine le début de la plage de pages à afficher
        $end = min($totalPages, $page + $range); // Détermine la plage de pages à afficher

        if ($start > 1) { // Vérifie si on n'est pas à la première page
            echo '<a href="' . pageLink(1) . '">1</a>'; //  Lien vers la première page
            if ($start > 2) echo ' ... '; // Ellipse si nécessaire
        }

        for ($i = $start; $i <= $end; $i++) { // Boucle pour afficher les pages dans la plage
            if ($i == $page) { // Si c'est la page courante
                echo '<strong>' . $i . '</strong> '; // Page courante, affichée en gras
            } else {
                echo '<a href="' . pageLink($i) . '">' . $i . '</a> '; // Lien vers la page courante
            }
        }

        if ($end < $totalPages) { // Vérifie si on est pas à la dernière page
            if ($end < $totalPages - 1) echo '... '; // Ellipse si nécessaire
            echo '<a href="' . pageLink($totalPages) . '">' . $totalPages . '</a>'; //  Lien vers la dernière page
        }

        if ($page < $totalPages) {
            echo ' <a href="' . pageLink($page + 1) . '">Suivant &raquo;</a>'; // Lien vers la page suivante
        }
        ?>
    <?php endif; ?>
</div>