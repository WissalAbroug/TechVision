<?php
class CV extends Model
{
    protected string $table = 'cvs';

    public function getByUserId(int $userId): ?array
    {
        $cv = $this->findBy('user_id', $userId);
        if ($cv) {
            $cv = $this->decode($cv);
        }
        return $cv;
    }

    private function decode(array $cv): array
    {
        $cv['competences'] = json_decode($cv['competences'] ?? '[]', true) ?: [];
        $cv['experiences'] = json_decode($cv['experiences'] ?? '[]', true) ?: [];
        $cv['educations']  = json_decode($cv['educations']  ?? '[]', true) ?: [];
        $cv['langues']     = json_decode($cv['langues']     ?? '[]', true) ?: [];
        $cv['soft_skills'] = json_decode($cv['soft_skills'] ?? '[]', true) ?: [];
        $cv['projets']     = json_decode($cv['projets']     ?? '[]', true) ?: [];
        $cv['realisations'] = json_decode($cv['realisations'] ?? '[]', true) ?: [];
        $cv['formations_complementaires'] = json_decode($cv['formations_complementaires'] ?? '[]', true) ?: [];
        $cv['reseaux']     = json_decode($cv['reseaux']     ?? '[]', true) ?: [];
        $cv['centres_interet'] = json_decode($cv['centres_interet'] ?? '[]', true) ?: [];
        $cv['certifications'] = json_decode($cv['certifications'] ?? '[]', true) ?: [];
        return $cv;
    }

    public function getSkillsAsString(int $userId): string
    {
        $cv = $this->getByUserId($userId);
        if (!$cv || empty($cv['competences'])) return '';
        return implode(',', $cv['competences']);
    }
}
