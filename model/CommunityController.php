<?php
class CommunityController {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getCommentsByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("
            SELECT id, message, created_at 
            FROM community_messages
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCommentById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM community_messages WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);
        return $comment ?: null;
    }

    public function updateComment(int $id, string $message): bool {
        $stmt = $this->pdo->prepare("UPDATE community_messages SET message = :message WHERE id = :id");
        return $stmt->execute(['message' => $message, 'id' => $id]);
    }
    
    public function isCommentAuthor(int $commentId, int $userId): bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM comments WHERE id = ? AND author_id = ?");
        $stmt->execute([$commentId, $userId]);
        return $stmt->fetchColumn() !== false;
    }

    public function deleteComment(int $commentId, int $userId): bool {
        // On supprime seulement si le commentaire appartient bien à l'utilisateur
        $stmt = $this->pdo->prepare("DELETE FROM community_messages WHERE id = ? AND user_id = ?");
        return $stmt->execute([$commentId, $userId]);
    }


}
?>