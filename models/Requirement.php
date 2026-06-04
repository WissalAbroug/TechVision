<?php
class Requirement extends Model
{
    protected string $table = 'requirements';

    public function deleteByOffre(int $offreId): bool
    {
        return $this->execute(
            "DELETE FROM `{$this->table}` WHERE offre_id = ?",
            [$offreId]
        );
    }
}
