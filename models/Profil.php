<?php
class Profil extends Model
{
    protected string $table = 'profils';

    public function getByUserId(int $userId): ?array
    {
        return $this->findBy('user_id', $userId);
    }

    /**
     * Insert or update profile for a user.
     * Called on registration (empty data) or profile edit.
     */
    public function createOrUpdate(int $userId, array $data): void
    {
        $existing = $this->getByUserId($userId);
        $data['user_id'] = $userId;
        if ($existing) {
            unset($data['user_id']);
            $this->update($existing['id'], $data);
        } else {
            $this->insert($data);
        }
    }
}
