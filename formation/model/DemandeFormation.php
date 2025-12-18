<?php
// Vérifier si la classe existe déjà avant de la déclarer
if (!class_exists('DemandeFormation')) {

    /**
     * Classe DemandeFormation - Modèle pour les inscriptions
     */
    class DemandeFormation
    {
        private ?int $id;
        private ?string $nom;
        private ?string $email;
        private ?string $tel;
        private ?int $formationId;
        private ?DateTime $dateInscription;
        private ?string $statut;
        private ?string $numeroDemande;
        private ?string $niveau;
        private ?string $modePaiement;

        /**
         * Constructeur
         */
        public function __construct(
            ?int $id = null,
            ?string $nom = null,
            ?string $email = null,
            ?string $tel = null,
            ?int $formationId = null,
            ?DateTime $dateInscription = null,
            ?string $statut = 'En attente',
            ?string $numeroDemande = null,
            ?string $niveau = null,
            ?string $modePaiement = 'Non spécifié'
        ) {
            $this->id = $id;
            $this->nom = $nom;
            $this->email = $email;
            $this->tel = $tel;
            $this->formationId = $formationId;
            $this->dateInscription = $dateInscription ?? new DateTime();
            $this->statut = $statut;
            $this->numeroDemande = $numeroDemande;
            $this->niveau = $niveau;
            $this->modePaiement = $modePaiement;
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

        public function getEmail(): ?string
        {
            return $this->email;
        }

        public function getTel(): ?string
        {
            return $this->tel;
        }

        public function getFormationId(): ?int
        {
            return $this->formationId;
        }

        public function getDateInscription(): ?DateTime
        {
            return $this->dateInscription;
        }

        public function getStatut(): ?string
        {
            return $this->statut;
        }

        public function getNumeroDemande(): ?string
        {
            return $this->numeroDemande;
        }

        public function getNiveau(): ?string
        {
            return $this->niveau;
        }

        public function getModePaiement(): ?string
        {
            return $this->modePaiement;
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

        public function setEmail(?string $email): void
        {
            $this->email = $email;
        }

        public function setTel(?string $tel): void
        {
            $this->tel = $tel;
        }

        public function setFormationId(?int $formationId): void
        {
            $this->formationId = $formationId;
        }

        public function setDateInscription(?DateTime $dateInscription): void
        {
            $this->dateInscription = $dateInscription;
        }

        public function setStatut(?string $statut): void
        {
            $this->statut = $statut;
        }

        public function setNumeroDemande(?string $numeroDemande): void
        {
            $this->numeroDemande = $numeroDemande;
        }

        public function setNiveau(?string $niveau): void
        {
            $this->niveau = $niveau;
        }

        public function setModePaiement(?string $modePaiement): void
        {
            $this->modePaiement = $modePaiement;
        }
    }
}
