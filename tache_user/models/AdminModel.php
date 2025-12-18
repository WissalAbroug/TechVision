<?php

class Admin {
    private ?int $id;
    private ?string $username;
    private ?string $password;
    private ?DateTime $createdAt;
    private ?DateTime $lastLogin;

    /**
     * Constructeur
     */
    public function __construct(
        ?int $id = null,
        ?string $username = null,
        ?string $password = null,
        ?DateTime $createdAt = null,
        ?DateTime $lastLogin = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->createdAt = $createdAt;
        $this->lastLogin = $lastLogin;
    }

    // ===============================================================
    // GETTERS
    // ===============================================================

    public function getId(): ?int {
        return $this->id;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }

    public function getLastLogin(): ?DateTime {
        return $this->lastLogin;
    }

    // ===============================================================
    // SETTERS
    // ===============================================================

    public function setId(?int $id): void {
        $this->id = $id;
    }

    public function setUsername(?string $username): void {
        $this->username = $username;
    }

    public function setPassword(?string $password): void {
        $this->password = $password;
    }

    public function setCreatedAt(?DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function setLastLogin(?DateTime $lastLogin): void {
        $this->lastLogin = $lastLogin;
    }

    // ===============================================================
    // MÉTHODE D'AFFICHAGE
    // ===============================================================

    /**
     * Affichage des informations de l'admin
     */
    public function show(): void {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Username</th><th>Password</th><th>Created At</th><th>Last Login</th></tr>";
        echo "<tr>";
        echo "<td>{$this->id}</td>";
        echo "<td>{$this->username}</td>";
        echo "<td>{$this->password}</td>";
        echo "<td>" . ($this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : "N/A") . "</td>";
        echo "<td>" . ($this->lastLogin ? $this->lastLogin->format('Y-m-d H:i:s') : "N/A") . "</td>";
        echo "</tr>";
        echo "</table>";
    }
}
?>