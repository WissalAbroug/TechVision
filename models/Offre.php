<?php
class Offre extends Model
{
    protected string $table = 'offres';

    /* ── Active offers with societe name ────────────────── */
    public function getActive(array $filters = []): array
    {
        $sql    = "SELECT o.*, u.nom_societe, u.prenom AS soc_nom, m.nom AS metier_nom
                   FROM offres o
                   JOIN users u ON o.societe_id = u.id
                   LEFT JOIN metiers m ON o.metier_id = m.id
                   WHERE o.statut = 'active'";
        $params = [];

        if (!empty($filters['q'])) {
            $sql   .= " AND (o.titre LIKE ? OR o.description LIKE ?)";
            $q      = '%' . $filters['q'] . '%';
            $params[] = $q; $params[] = $q;
        }
        if (!empty($filters['type_contrat'])) {
            $sql   .= " AND o.type_contrat = ?";
            $params[] = $filters['type_contrat'];
        }
        if (!empty($filters['localisation'])) {
            $sql   .= " AND o.localisation LIKE ?";
            $params[] = '%' . $filters['localisation'] . '%';
        }
        $sql .= " ORDER BY o.created_at DESC";
        return $this->query($sql, $params);
    }

    /* ── Single offer with societe info ──────────────────── */
    public function getWithSociete(int $id): ?array
    {
        return $this->queryOne(
            "SELECT o.*, u.nom_societe, u.prenom AS soc_nom, m.nom AS metier_nom
             FROM offres o
             JOIN users u ON o.societe_id = u.id
             LEFT JOIN metiers m ON o.metier_id = m.id
             WHERE o.id = ?",
            [$id]
        );
    }

    /* ── All offers for a societe user ───────────────────── */
    public function getBySociete(int $userId): array
    {
        return $this->query(
            "SELECT o.*,
                    (SELECT COUNT(*) FROM candidatures c WHERE c.offre_id = o.id) AS nb_candidatures,
                    m.nom AS metier_nom
             FROM offres o
             LEFT JOIN metiers m ON o.metier_id = m.id
             WHERE o.societe_id = ?
             ORDER BY o.created_at DESC",
            [$userId]
        );
    }

    /* ── Admin: all with societe name ─────────────────────── */
    public function getAllWithSociete(string $statut = ''): array
    {
        $sql    = "SELECT o.*, u.nom_societe, u.prenom AS soc_nom
                   FROM offres o JOIN users u ON o.societe_id = u.id";
        $params = [];
        if ($statut) { $sql .= " WHERE o.statut = ?"; $params[] = $statut; }
        $sql .= " ORDER BY o.created_at DESC";
        return $this->query($sql, $params);
    }

    /* ── Count active ────────────────────────────────────── */
    public function countActive(): int
    {
        return $this->count("statut = 'active'");
    }
}
