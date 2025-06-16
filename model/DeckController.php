<?php
require_once 'Deck.php';
require_once 'config.php';

class DeckController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
        
    }

    public function getAllDecks(): array {
        $stmt = $this->db->query("SELECT * FROM decks ORDER BY created_at DESC");
        $result = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Deck($row); // on hydrate un objet Deck à chaque ligne
        }

        return $result;
    }

    public function addDeck(Deck $deck): void {
        $stmt = $this->db->prepare("INSERT INTO decks (title, description,author_id) VALUES (?, ?, ?)");
        $stmt->execute([$deck->getTitle(), $deck->getDescription(), $deck->getAuthorId()]);
    }

    // Méthode pour récupérer les cartes d'un deck par son ID
    public function getCardsByDeckId($deckId) {
        $stmt = $this->db->prepare("
            SELECT cards.name, cards.image_url
            FROM cards
            INNER JOIN deck_cards ON deck_cards.card_id = cards.id
            WHERE deck_cards.deck_id = ?
        ");
        $stmt->execute([$deckId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isFavorite($userId, $deckId) {
        $stmt = $this->db->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND deck_id = ?");
        $stmt->execute([$userId, $deckId]);
        return $stmt->fetchColumn() !== false;
    }

    public function addFavorite(int $userId, int $deckId): bool {
        // Vérifie si le favori existe déjà
        $stmt = $this->db->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND deck_id = ?");
        $stmt->execute([$userId, $deckId]);
        if ($stmt->fetch()) {
            return false; // Favori déjà ajouté
        }

        // Ajoute le favori
        $stmt = $this->db->prepare("INSERT INTO favorites (user_id, deck_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $deckId]);
    }

    public function removeFavorite(int $userId, int $deckId): bool {
        $stmt = $this->db->prepare("DELETE FROM favorites WHERE user_id = ? AND deck_id = ?");
        return $stmt->execute([$userId, $deckId]);
    }


    public function getFavoriteDecksByUserId(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT d.*
            FROM favorites f
            INNER JOIN decks d ON d.id = f.deck_id
            WHERE f.user_id = ?
            ORDER BY d.created_at DESC
        ");
        $stmt->execute([$userId]);
        $decks = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $decks[] = new Deck($row);
        }

        return $decks;
    }

    public function getFilteredAndSortedDecks($search, $sort, $limit = 10, $offset = 0) {
        $query = "SELECT * FROM decks WHERE title LIKE :search";

        switch ($sort) {
            case 'oldest':
                $query .= " ORDER BY created_at ASC";
                break;
            case 'az':
                $query .= " ORDER BY title ASC";
                break;
            case 'za':
                $query .= " ORDER BY title DESC";
                break;
            default:
                $query .= " ORDER BY created_at DESC";
                break;
        }

        $query .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Deck');
        return $stmt->fetchAll();
    }

    public function countFilteredDecks(string $search): int{
        $sql = "SELECT COUNT(*) FROM decks WHERE title LIKE :search OR description LIKE :search";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['search' => '%' . $search . '%']);
        return (int)$stmt->fetchColumn();
    }

    public function getDecksByUserId(int $userId, string $search = '', string $sort = 'desc'): array {
        $order = ($sort === 'asc' || $sort === 'oldest') ? 'ASC' : 'DESC';

        $sql = "SELECT * FROM decks WHERE author_id = :user_id";

        if ($search !== '') {
            $sql .= " AND title LIKE :search";
        }

        $sql .= " ORDER BY created_at $order";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        if ($search !== '') {
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        }

        $stmt->execute();

        $decks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $decks[] = new Deck($row);
        }
        return $decks;
    }

    public function countFilteredDecksByUserId(int $userId, string $search = ''): int {
        $sql = "SELECT COUNT(*) FROM decks WHERE author_id = :user_id";
        if ($search !== '') {
            $sql .= " AND title LIKE :search";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        if ($search !== '') {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getFilteredAndSortedDecksByUserId(int $userId, string $search = '', string $sort = 'desc', int $limit = 10, int $offset = 0): array {
        $order = 'DESC';
        if (in_array(strtolower($sort), ['asc', 'oldest'])) {
            $order = 'ASC';
        } elseif (strtolower($sort) === 'az') {
            $orderBy = 'title ASC';
        } elseif (strtolower($sort) === 'za') {
            $orderBy = 'title DESC';
        }

        // Si $orderBy n'est pas défini, trier par created_at
        if (!isset($orderBy)) {
            $orderBy = "created_at $order";
        }

        $sql = "SELECT * FROM decks WHERE author_id = :user_id";
        if ($search !== '') {
            $sql .= " AND title LIKE :search";
        }
        $sql .= " ORDER BY $orderBy LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        if ($search !== '') {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $decks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $decks[] = new Deck($row);
        }
        return $decks;
    }

    // Récupérer un deck par son id
    public function getDeckById(int $id): ?Deck {
        $stmt = $this->db->prepare("SELECT * FROM decks WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new Deck($data);
        }
        return null;
    }

    // Mettre à jour un deck
    public function updateDeck(int $deckId, string $title, string $description): bool {
        $sql = "UPDATE decks SET title = :title, description = :description WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':id' => $deckId,
        ]);
    }

    // Supprimer un deck
    public function deleteDeck(int $deckId): bool {
        $stmt = $this->db->prepare("DELETE FROM decks WHERE id = ?");
        return $stmt->execute([$deckId]);
    }

    
}