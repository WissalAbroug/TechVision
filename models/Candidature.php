<?php
class Candidature extends Model
{
    protected string $table = 'candidatures';

    /* ── User's applications with offer info ─────────────── */
    public function getByUserWithOffre(int $userId, int $limit = 0): array
    {
        $sql = "SELECT c.*,
                       o.titre AS offre_titre,
                       o.type_contrat,
                       u.nom_societe, u.prenom AS soc_nom
                FROM candidatures c
                JOIN offres o ON c.offre_id  = o.id
                JOIN users  u ON o.societe_id = u.id
                WHERE c.user_id = ?
                ORDER BY c.date_candidature DESC";
        if ($limit > 0) $sql .= " LIMIT {$limit}";
        return $this->query($sql, [$userId]);
    }

    /* ── Offer candidates with user & CV info ────────────── */
    public function getByOffreWithProfile(int $offreId): array
    {
        return $this->query(
            "SELECT c.*,
                    u.prenom, u.nom, u.email,
                    p.titre AS profil_titre,
                    cv.id  AS cv_id
             FROM candidatures c
             JOIN users u ON c.user_id = u.id
             LEFT JOIN profils p  ON p.user_id = u.id
             LEFT JOIN cvs    cv  ON cv.user_id = u.id
             WHERE c.offre_id = ?
             ORDER BY c.score_matching DESC",
            [$offreId]
        );
    }

    /* ── Check if user already applied ───────────────────── */
    public function hasApplied(int $userId, int $offreId): bool
    {
        return $this->count("user_id = ? AND offre_id = ?", [$userId, $offreId]) > 0;
    }

    /* ── Count by societe (via societe_id on offres) ─────── */
    public function countBySociete(int $societeId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM candidatures c
             JOIN offres o ON c.offre_id = o.id
             WHERE o.societe_id = ?"
        );
        $stmt->execute([$societeId]);
        return (int)$stmt->fetchColumn();
    }

    /* ── Update status ───────────────────────────────────── */
    public function updateStatus(int $id, string $statut): bool
    {
        return $this->update($id, ['statut' => $statut]);
    }
}
