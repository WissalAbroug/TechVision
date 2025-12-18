<?php
class DemandeEntretien {
    private ?int $id;
    private ?string $nom;
    private ?string $email;
    private ?string $telephone;
    private ?string $statut;
    private ?string $date_creation;

    // Constructor
    public function __construct(?int $id, ?string $nom, ?string $email, ?string $telephone, ?string $statut, ?string $date_creation) {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->statut = $statut;
        $this->date_creation = $date_creation;
    }

    // Getters and Setters
    public function getId(): ?int {
        return $this->id;
    }

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function getNom(): ?string {
        return $this->nom;
    }

    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    public function getTelephone(): ?string {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): void {
        $this->telephone = $telephone;
    }

    public function getStatut(): ?string {
        return $this->statut;
    }

    public function setStatut(?string $statut): void {
        $this->statut = $statut;
    }

    public function getDateCreation(): ?string {
        return $this->date_creation;
    }

    public function setDateCreation(?string $date_creation): void {
        $this->date_creation = $date_creation;
    }
}
?>