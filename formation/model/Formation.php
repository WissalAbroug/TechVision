<?php
// Vérifier si la classe existe déjà avant de la déclarer
if (!class_exists('Formation')) {

    /**
     * Classe Formation - Modèle pour les formations professionnelles
     */
    class Formation
    {
        private ?int $id;
        private ?string $nom;
        private ?DateTime $dateFormation;
        private ?string $niveau;
        private ?int $placesMax;
        private ?int $placesPrises;
        private ?float $prix;
        private ?string $description;
        private ?string $statut;

        /**
         * Constructeur
         */
        public function __construct(
            ?int $id = null,
            ?string $nom = null,
            ?DateTime $dateFormation = null,
            ?string $niveau = null,
            ?int $placesMax = null,
            ?int $placesPrises = 0,
            ?float $prix = null,
            ?string $description = null,
            ?string $statut = 'Active'
        ) {
            $this->id = $id;
            $this->nom = $nom;
            $this->dateFormation = $dateFormation;
            $this->niveau = $niveau;
            $this->placesMax = $placesMax;
            $this->placesPrises = $placesPrises;
            $this->prix = $prix;
            $this->description = $description;
            $this->statut = $statut;
        }

        // ========== GETTERS ==========
        public function getId(): ?int
        {
            return $this->id;
        }

        public function getNom(): ?string
        {
            return $this->nom;
        }

        public function getDateFormation(): ?DateTime
        {
            return $this->dateFormation;
        }

        public function getNiveau(): ?string
        {
            return $this->niveau;
        }

        public function getPlacesMax(): ?int
        {
            return $this->placesMax;
        }

        public function getPlacesPrises(): ?int
        {
            return $this->placesPrises;
        }

        public function getPrix(): ?float
        {
            return $this->prix;
        }

        public function getDescription(): ?string
        {
            return $this->description;
        }

        public function getStatut(): ?string
        {
            return $this->statut;
        }

        // ========== SETTERS ==========
        public function setId(?int $id): void
        {
            $this->id = $id;
        }

        public function setNom(?string $nom): void
        {
            $this->nom = $nom;
        }

        public function setDateFormation(?DateTime $dateFormation): void
        {
            $this->dateFormation = $dateFormation;
        }

        public function setNiveau(?string $niveau): void
        {
            $this->niveau = $niveau;
        }

        public function setPlacesMax(?int $placesMax): void
        {
            $this->placesMax = $placesMax;
        }

        public function setPlacesPrises(?int $placesPrises): void
        {
            $this->placesPrises = $placesPrises;
        }

        public function setPrix(?float $prix): void
        {
            $this->prix = $prix;
        }

        public function setDescription(?string $description): void
        {
            $this->description = $description;
        }

        public function setStatut(?string $statut): void
        {
            $this->statut = $statut;
        }
    }
} // Fin du if !class_exists