<?php
class Entretien extends Model
{
    protected string $table = 'entretiens';

    /* ── User upcoming interviews ────────────────────────── */
    public function getUpcomingForUser(int $userId): array
    {
        return $this->query(
            "SELECT e.*,
                    o.titre AS offre_titre,
                    u.nom_societe, u.prenom AS soc_nom
             FROM entretiens e
             JOIN candidatures c ON e.candidature_id = c.id
             JOIN offres o       ON c.offre_id  = o.id
             JOIN users  u       ON o.societe_id = u.id
             WHERE c.user_id = ?
               AND e.statut IN ('planifie','confirme')
               AND e.date_entretien >= NOW()
             ORDER BY e.date_entretien ASC
             LIMIT 5",
            [$userId]
        );
    }

    /* ── All interviews for a user (index page) ──────────── */
    public function getForUser(int $userId): array
    {
        return $this->query(
            "SELECT e.*,
                    o.titre AS offre_titre,
                    u.nom_societe, u.prenom AS soc_nom
             FROM entretiens e
             JOIN candidatures c ON e.candidature_id = c.id
             JOIN offres o       ON c.offre_id  = o.id
             JOIN users  u       ON o.societe_id = u.id
             WHERE c.user_id = ?
             ORDER BY e.date_entretien DESC",
            [$userId]
        );
    }

    /* ── Single interview with full details ──────────────── */
    public function getWithDetails(int $id): ?array
    {
        return $this->queryOne(
            "SELECT e.*,
                    o.titre AS offre_titre, o.metier_id,
                    u.nom_societe, u.prenom AS u_soc_nom,
                    c.user_id AS candidat_user_id
             FROM entretiens e
             JOIN candidatures c ON e.candidature_id = c.id
             JOIN offres o       ON c.offre_id  = o.id
             JOIN users  u       ON o.societe_id = u.id
             WHERE e.id = ?",
            [$id]
        );
    }

    /* ── All interviews for a societe ────────────────────── */
    public function getBySociete(int $societeId): array
    {
        return $this->query(
            "SELECT e.*,
                    o.titre AS offre_titre,
                    cu.prenom AS candidat_prenom,
                    cu.nom    AS candidat_nom,
                    cu.email  AS candidat_email
             FROM entretiens e
             JOIN candidatures c ON e.candidature_id = c.id
             JOIN offres o       ON c.offre_id  = o.id
             JOIN users  cu      ON c.user_id   = cu.id
             WHERE o.societe_id = ?
             ORDER BY e.date_entretien DESC",
            [$societeId]
        );
    }

    /* ── Count for societe ───────────────────────────────── */
    public function countBySociete(int $societeId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM entretiens e
             JOIN candidatures c ON e.candidature_id = c.id
             JOIN offres o       ON c.offre_id = o.id
             WHERE o.societe_id = ?"
        );
        $stmt->execute([$societeId]);
        return (int)$stmt->fetchColumn();
    }

    /* ── All interviews for Admin ────────────────────────── */
    public function getAllAdmin(): array
    {
        return $this->query(
            "SELECT e.*, 
                   cu.nom AS candidat_nom, cu.prenom AS candidat_prenom, cu.email AS candidat_email,
                   o.titre AS offre_titre,
                   su.nom_societe AS nom_entreprise
            FROM entretiens e
            JOIN candidatures c ON e.candidature_id = c.id
            JOIN users cu ON c.user_id = cu.id
            JOIN offres o ON c.offre_id = o.id
            JOIN users su ON o.societe_id = su.id
            ORDER BY e.date_entretien DESC"
        );
    }
}
