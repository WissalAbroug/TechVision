<?php
class Formation extends Model
{
    protected string $table = 'formations';

    /* ── Enrolled formations for a user ─────────────────── */
    public function getEnrolled(int $userId): array
    {
        return $this->query(
            "SELECT f.*, uf.statut, uf.progression, uf.date_debut, uf.date_fin, uf.formation_id
             FROM user_formations uf
             JOIN formations f ON uf.formation_id = f.id
             WHERE uf.user_id = ?
             ORDER BY uf.date_debut DESC",
            [$userId]
        );
    }

    /* ── Is user enrolled ────────────────────────────────── */
    public function isEnrolled(int $userId, int $formationId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM user_formations WHERE user_id = ? AND formation_id = ?"
        );
        $stmt->execute([$userId, $formationId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /* ── Enroll user ─────────────────────────────────────── */
    public function enroll(int $userId, int $formationId): void
    {
        $this->execute(
            "INSERT INTO user_formations (user_id, formation_id, statut, progression) VALUES (?, ?, 'non_commence', 0)",
            [$userId, $formationId]
        );
    }

    /* ── Check if has start date ─────────────────────────── */
    public function hasStartDate(int $userId, int $formationId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT date_debut FROM user_formations WHERE user_id = ? AND formation_id = ?"
        );
        $stmt->execute([$userId, $formationId]);
        $row = $stmt->fetch();
        return !empty($row['date_debut']);
    }

    /* ── Update progress ─────────────────────────────────── */
    public function updateProgress(int $userId, int $formationId, array $data): void
    {
        $set    = implode(', ', array_map(fn($k) => "`{$k}` = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $userId;
        $values[] = $formationId;
        $this->execute(
            "UPDATE user_formations SET {$set} WHERE user_id = ? AND formation_id = ?",
            $values
        );
    }

    /* ── AI Recommendations based on missing skills ──────── */
    public function getRecommended(int $userId, int $limit = 6): array
    {
        // Get user CV skills
        $stmt = $this->db->prepare(
            "SELECT competences FROM cvs WHERE user_id = ? LIMIT 1"
        );
        $stmt->execute([$userId]);
        $cv = $stmt->fetch();
        $userSkills = [];
        if ($cv) {
            $userSkills = json_decode($cv['competences'] ?? '[]', true) ?: [];
            $userSkills = array_map('strtolower', $userSkills);
        }

        // Get all formations
        $formations = $this->findAll('created_at DESC');
        $enrolledIds = array_column($this->getEnrolled($userId), 'formation_id');

        // Score each formation
        $scored = [];
        foreach ($formations as $f) {
            if (in_array($f['id'], $enrolledIds)) continue;
            $targets = json_decode($f['competences_cibles'] ?? '[]', true) ?: [];
            $targets = array_map('strtolower', $targets);

            // Higher score = more new skills (not already owned)
            $newSkills = array_diff($targets, $userSkills);
            $scored[]  = array_merge($f, ['relevance' => count($newSkills)]);
        }

        usort($scored, fn($a, $b) => $b['relevance'] <=> $a['relevance']);
        return array_slice($scored, 0, $limit);
    }
}
