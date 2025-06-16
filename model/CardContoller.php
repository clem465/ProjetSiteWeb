<?php
class CardController
{
    private array $cards = [];

    public function __construct(string $jsonFilePath = null)
    {
        if ($jsonFilePath && file_exists($jsonFilePath)) {
            $jsonContent = file_get_contents($jsonFilePath);
            $this->cards = json_decode($jsonContent, true) ?? [];
        } else {
            // Par défaut, initialiser à un tableau vide ou appeler une méthode pour récupérer autrement
            $this->cards = [];
        }
    }

    /**
     * Récupère toutes les cartes
     * @return array
     */
    public function getAllCards(): array
    {
        return $this->cards;
    }

    /**
     * Filtre et trie les cartes selon les critères
     * @param string|null $search Recherche par nom (partiel, insensible à la casse)
     * @param string|null $sort Tri possible : 'name', 'elixir', 'rarity'
     * @return array Cartes filtrées et triées
     */
    public function getFilteredCards(?string $search = null, ?string $sort = null): array
    {
        $filtered = $this->cards;

        // Filtrer par nom
        if ($search !== null && $search !== '') {
            $filtered = array_filter($filtered, function ($card) use ($search) {
                return stripos($card['name'] ?? '', $search) !== false;
            });
        }

        // Trier selon le critère
        if ($sort === 'name') {
            usort($filtered, fn($a, $b) => strcmp($a['name'] ?? '', $b['name'] ?? ''));
        } elseif ($sort === 'elixir') {
            usort($filtered, fn($a, $b) => ($a['elixirCost'] ?? 0) <=> ($b['elixirCost'] ?? 0));
        } elseif ($sort === 'rarity') {
            $rarityOrder = ['Common' => 1, 'Rare' => 2, 'Epic' => 3, 'Legendary' => 4];
            usort($filtered, function ($a, $b) use ($rarityOrder) {
                $rA = $rarityOrder[$a['rarity']] ?? 0;
                $rB = $rarityOrder[$b['rarity']] ?? 0;
                return $rA <=> $rB;
            });
        }

        return $filtered;
    }

    /**
     * Récupère uniquement les cartes de type "Support"
     * @return array
     */
    public function getSupportCards(): array
    {
        return array_filter($this->cards, fn($card) => ($card['type'] ?? '') === 'Support');
    }
}
