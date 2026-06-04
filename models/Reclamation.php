<?php
class Reclamation extends Model
{
    protected string $table = 'reclamations';

    /* ── All reclamations with user info ─────────────────── */
    public function getAllWithUser(): array
    {
        return $this->query(
            "SELECT r.*, u.prenom, u.nom, u.email
             FROM reclamations r
             JOIN users u ON r.user_id = u.id
             ORDER BY r.created_at DESC"
        );
    }

    /* ── Get reclamation with user ───────────────────────── */
    public function getWithUser(int $id): ?array
    {
        return $this->queryOne(
            "SELECT r.*, u.prenom, u.nom, u.email
             FROM reclamations r
             JOIN users u ON r.user_id = u.id
             WHERE r.id = ?",
            [$id]
        );
    }

    /* ── User's own reclamations ─────────────────────────── */
    public function getByUser(int $userId): array
    {
        return $this->findAllBy('user_id', $userId, 'created_at DESC');
    }

    /* ── Add reply ───────────────────────────────────────── */
    public function addReply(array $data): bool
    {
        return $this->execute(
            "INSERT INTO reponses (reclamation_id, auteur_id, message)
             VALUES (?, ?, ?)",
            [$data['reclamation_id'], $data['auteur_id'], $data['message']]
        );
    }

    /* ── Get replies for a reclamation ──────────────────── */
    public function getReplies(int $reclamationId): array
    {
        return $this->query(
            "SELECT rr.*, u.prenom, u.nom, u.role
             FROM reponses rr
             JOIN users u ON rr.auteur_id = u.id
             WHERE rr.reclamation_id = ?
             ORDER BY rr.created_at ASC",
            [$reclamationId]
        );
    }
}
