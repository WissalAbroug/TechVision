<?php
// Modèle DemandeEntretien - Représente une réservation de candidat
class DemandeEntretien {
    private ?int $id;
    private ?string $nom;
    private ?string $tel;
    private ?string $email;
    private ?int $entretienId;
    private ?DateTime $dateDemande;
    private ?string $statut;

    // Constructeur
    public function __construct(
        ?int $id = null,
        ?string $nom = null,
        ?string $tel = null,
        ?string $email = null,
        ?int $entretienId = null,
        ?DateTime $dateDemande = null,
        ?string $statut = 'En attente'
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->tel = $tel;
        $this->email = $email;
        $this->entretienId = $entretienId;
        $this->dateDemande = $dateDemande;
        $this->statut = $statut;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function getTel(): ?string {
        return $this->tel;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function getEntretienId(): ?int {
        return $this->entretienId;
    }

    public function getDateDemande(): ?DateTime {
        return $this->dateDemande;
    }

    public function getStatut(): ?string {
        return $this->statut;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }

    public function setTel(?string $tel): void {
        $this->tel = $tel;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function setEntretienId(?int $entretienId): void {
        $this->entretienId = $entretienId;
    }

    public function setDateDemande(?DateTime $dateDemande): void {
        $this->dateDemande = $dateDemande;
    }

    public function setStatut(?string $statut): void {
        $this->statut = $statut;
    }

    // Méthodes utilitaires
    public function getDateDemandeFormatee(): string {
        return $this->dateDemande ? $this->dateDemande->format('d/m/Y H:i') : '';
    }

    public function getStatutBadgeClass(): string {
        switch($this->statut) {
            case 'Confirmé':
                return 'success-btn';
            case 'Annulé':
                return 'close-btn';
            default:
                return 'warning-btn';
        }
    }
}
?>