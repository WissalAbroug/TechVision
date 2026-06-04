<?php
class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function existsByEmail(string $email): bool
    {
        return $this->count('email = ?', [$email]) > 0;
    }

    public function verifyPassword(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }

    public function register(array $data): int
    {
        return $this->insert([
            'nom'        => trim($data['nom']    ?? ''),
            'prenom'     => trim($data['prenom'] ?? ''),
            'email'      => strtolower(trim($data['email'] ?? '')),
            'password'   => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'       => $data['role'] ?? 'candidat',
            'statut'     => 'actif',
            'nom_societe'=> trim($data['nom_societe'] ?? ''),
            'secteur'    => trim($data['secteur']     ?? ''),
        ]);
    }

    public function getRecentRegistrations(int $limit = 5): array
    {
        return $this->findAll('created_at DESC', $limit);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        return $this->update($id, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT)
        ]);
    }
}
