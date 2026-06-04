<?php

require_once 'Model.php';

class QuizResult extends Model
{
    protected string $table = 'quiz_results';

    /**
     * Get results with quiz details for a specific user
     */
    public function getResultsByUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, q.titre, q.type, q.cible, q.niveau 
            FROM {$this->table} r
            JOIN quizzes q ON r.quiz_id = q.id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Save a new quiz result
     */
    public function saveResult(int $userId, int $quizId, int $score, int $total, array $recommandations = []): int
    {
        return $this->insert([
            'user_id' => $userId,
            'quiz_id' => $quizId,
            'score' => $score,
            'total' => $total,
            'recommandations' => json_encode($recommandations)
        ]);
    }
}
