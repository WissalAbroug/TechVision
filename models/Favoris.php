<?php
class Favoris extends Model
{
    protected string $table = 'favoris';

    public function isFavorited(int $userId, int $offreId): bool
    {
        return $this->count("user_id = ? AND offre_id = ?", [$userId, $offreId]) > 0;
    }

    public function toggle(int $userId, int $offreId): bool
    {
        if ($this->isFavorited($userId, $offreId)) {
            $stmt = $this->db->prepare("DELETE FROM favoris WHERE user_id = ? AND offre_id = ?");
            $stmt->execute([$userId, $offreId]);
            return false;
        } else {
            $this->insert([
                'user_id' => $userId,
                'offre_id' => $offreId
            ]);
            return true;
        }
    }

    public function getByUser(int $userId): array
    {
        return $this->query(
            "SELECT o.*, u.nom_societe, u.prenom AS soc_nom, m.nom AS metier_nom
             FROM favoris f
             JOIN offres o ON f.offre_id = o.id
             JOIN users u ON o.societe_id = u.id
             LEFT JOIN metiers m ON o.metier_id = m.id
             WHERE f.user_id = ?
             ORDER BY f.created_at DESC",
            [$userId]
        );
    }
}
