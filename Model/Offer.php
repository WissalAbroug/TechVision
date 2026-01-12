<?php
/**
 * Classe Offer - Modèle représentant une offre d'emploi
 * Encapsule toutes les données relatives à une offre
 */

class Offer {
    // Propriétés privées
    private $id;
    private $titre;
    private $description;
    private $nomSociete;
    private $localisation;
    private $salaireMin;
    private $salaireMax;
    private $typeContrat;
    private $experienceRequise;
    private $competences;
    private $requirements;
    private $nbPlace;
    private $dateLimite;
    private $dateCreation;
    private $statut;

    /**
     * Constructeur
     */
    public function __construct(
        $id = null,
        $titre = "",
        $description = "",
        $nomSociete = "",
        $localisation = "",
        $salaireMin = 0,
        $salaireMax = 0,
        $typeContrat = "CDI",
        $experienceRequise = "",
        $competences = [],
        $requirements = [],
        $nbPlace = 1,
        $dateLimite = null,
        $dateCreation = null,
        $statut = "active"
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->nomSociete = $nomSociete;
        $this->localisation = $localisation;
        $this->salaireMin = $salaireMin;
        $this->salaireMax = $salaireMax;
        $this->typeContrat = $typeContrat;
        $this->experienceRequise = $experienceRequise;
        $this->competences = is_array($competences) ? $competences : [];
        $this->requirements = is_array($requirements) ? $requirements : [];
        $this->nbPlace = $nbPlace;
        $this->dateLimite = $dateLimite;
        $this->dateCreation = $dateCreation ?? date('Y-m-d H:i:s');
        $this->statut = $statut;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getNomSociete() { return $this->nomSociete; }
    public function getLocalisation() { return $this->localisation; }
    public function getSalaireMin() { return $this->salaireMin; }
    public function getSalaireMax() { return $this->salaireMax; }
    public function getTypeContrat() { return $this->typeContrat; }
    public function getExperienceRequise() { return $this->experienceRequise; }
    public function getCompetences() { return $this->competences; }
    public function getRequirements() { return $this->requirements; }
    public function getNbPlace() { return $this->nbPlace; }
    public function getDateLimite() { return $this->dateLimite; }
    public function getDateCreation() { return $this->dateCreation; }
    public function getStatut() { return $this->statut; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setNomSociete($nomSociete) { $this->nomSociete = $nomSociete; }
    public function setLocalisation($localisation) { $this->localisation = $localisation; }
    public function setSalaireMin($salaireMin) { $this->salaireMin = $salaireMin; }
    public function setSalaireMax($salaireMax) { $this->salaireMax = $salaireMax; }
    public function setTypeContrat($typeContrat) { $this->typeContrat = $typeContrat; }
    public function setExperienceRequise($experienceRequise) { $this->experienceRequise = $experienceRequise; }
    public function setCompetences($competences) { $this->competences = $competences; }
    public function setRequirements($requirements) { $this->requirements = $requirements; }
    public function setNbPlace($nbPlace) { $this->nbPlace = $nbPlace; }
    public function setDateLimite($dateLimite) { $this->dateLimite = $dateLimite; }
    public function setDateCreation($dateCreation) { $this->dateCreation = $dateCreation; }
    public function setStatut($statut) { $this->statut = $statut; }

    /**
     * Convertir l'objet en tableau associatif
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'nomSociete' => $this->nomSociete,
            'localisation' => $this->localisation,
            'salaireMin' => $this->salaireMin,
            'salaireMax' => $this->salaireMax,
            'typeContrat' => $this->typeContrat,
            'experienceRequise' => $this->experienceRequise,
            'competences' => $this->competences,
            'requirements' => $this->requirements,
            'nbPlace' => $this->nbPlace,
            'dateLimite' => $this->dateLimite,
            'dateCreation' => $this->dateCreation,
            'statut' => $this->statut
        ];
    }
}
?>