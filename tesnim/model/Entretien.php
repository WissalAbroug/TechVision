<?php
// Modèle Entretien - Représente une session d'entretien
class Entretien {
    private ?int $id;
    private ?string $type;
    private ?DateTime $date;
    private ?string $heure;
    private ?int $places;
    private ?int $placesPrises;

    // Constructeur
    public function __construct(
        ?int $id = null,
        ?string $type = null,
        ?DateTime $date = null,
        ?string $heure = null,
        ?int $places = 10,
        ?int $placesPrises = 0
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->date = $date;
        $this->heure = $heure;
        $this->places = $places;
        $this->placesPrises = $placesPrises;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getType(): ?string {
        return $this->type;
    }

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function getHeure(): ?string {
        return $this->heure;
    }

    public function getPlaces(): ?int {
        return $this->places;
    }

    public function getPlacesPrises(): ?int {
        return $this->placesPrises;
    }

    // Setters
    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setType(?string $type): void {
        $this->type = $type;
    }

    public function setDate(?DateTime $date): void {
        $this->date = $date;
    }

    public function setHeure(?string $heure): void {
        $this->heure = $heure;
    }

    public function setPlaces(?int $places): void {
        $this->places = $places;
    }

    public function setPlacesPrises(?int $placesPrises): void {
        $this->placesPrises = $placesPrises;
    }

    // Méthodes utilitaires
    public function getPlacesRestantes(): int {
        return $this->places - $this->placesPrises;
    }

    public function estComplet(): bool {
        return $this->placesPrises >= $this->places;
    }

    public function estPasse(): bool {
        $maintenant = new DateTime();
        return $this->date < $maintenant;
    }

    public function getDateFormatee(): string {
        return $this->date ? $this->date->format('d/m/Y') : '';
    }

    public function getHeureFormatee(): string {
        return substr($this->heure, 0, 5); // Format HH:MM
    }
}
?>