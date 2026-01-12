<?php
/**
 * Classe Category - Modèle représentant une catégorie de métiers
 * Encapsule toutes les données relatives à une catégorie
 */

class Category {
    // Propriétés privées
    private $id;
    private $nom;
    private $description;
    private $icone;
    private $dateCreation;
    private $dateModification;
    private $nbOffres; // Nombre d'offres associées (calculé)

    /**
     * Constructeur
     */
    public function __construct(
        $id = null,
        $nom = "",
        $description = "",
        $icone = null,
        $dateCreation = null,
        $dateModification = null,
        $nbOffres = 0
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->icone = $icone;
        $this->dateCreation = $dateCreation ?? date('Y-m-d H:i:s');
        $this->dateModification = $dateModification ?? date('Y-m-d H:i:s');
        $this->nbOffres = $nbOffres;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getDescription() { return $this->description; }
    public function getIcone() { return $this->icone; }
    public function getDateCreation() { return $this->dateCreation; }
    public function getDateModification() { return $this->dateModification; }
    public function getNbOffres() { return $this->nbOffres; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setDescription($description) { $this->description = $description; }
    public function setIcone($icone) { $this->icone = $icone; }
    public function setDateCreation($dateCreation) { $this->dateCreation = $dateCreation; }
    public function setDateModification($dateModification) { $this->dateModification = $dateModification; }
    public function setNbOffres($nbOffres) { $this->nbOffres = $nbOffres; }

    /**
     * Validation des données
     * @return array Tableau des erreurs (vide si aucune erreur)
     */
    public function validate() {
        $errors = [];

        if (empty(trim($this->nom))) {
            $errors[] = "Le nom de la catégorie est obligatoire";
        } elseif (strlen($this->nom) < 3) {
            $errors[] = "Le nom doit contenir au moins 3 caractères";
        } elseif (strlen($this->nom) > 100) {
            $errors[] = "Le nom ne peut pas dépasser 100 caractères";
        }

        if (!empty($this->description) && strlen($this->description) > 1000) {
            $errors[] = "La description ne peut pas dépasser 1000 caractères";
        }

        if (!empty($this->icone) && strlen($this->icone) > 100) {
            $errors[] = "L'icône ne peut pas dépasser 100 caractères";
        }

        return $errors;
    }

    /**
     * Convertir l'objet en tableau associatif
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'icone' => $this->icone,
            'dateCreation' => $this->dateCreation,
            'dateModification' => $this->dateModification,
            'nbOffres' => $this->nbOffres
        ];
    }
}
?>