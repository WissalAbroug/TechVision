<?php

require_once 'Model.php';

class Quiz extends Model
{
    protected string $table = 'quizzes';

    /**
     * Get all questions for a specific quiz
     */
    public function getQuestions(int $quizId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY id ASC");
        $stmt->execute([$quizId]);
        return $stmt->fetchAll();
    }

    /**
     * Insert a question into a quiz
     */
    public function addQuestion(int $quizId, string $question, array $options, int $correctOption, ?string $explication = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO quiz_questions (quiz_id, question, options, correct_option, explication)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$quizId, $question, json_encode($options), $correctOption, $explication]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Get tests available for a user (Generic + User specific)
     */
    public function getAvailableForUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id IS NULL OR user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
