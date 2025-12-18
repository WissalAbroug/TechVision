<?php
require_once __DIR__ . '/../config/Database.php';

class UserModel {
    private ?int $id;
    private ?string $username;
    private ?string $email;
    private ?string $password;
    private ?string $profilePhoto;
    private ?DateTime $createdAt;
    private $pdo;
    private string $table = "compte";

    /**
     * Constructeur avec tous les paramètres
     */
    public function __construct(
        ?int $id = null,
        ?string $username = null,
        ?string $email = null,
        ?string $password = null,
        ?string $profilePhoto = null,
        ?DateTime $createdAt = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->profilePhoto = $profilePhoto;
        $this->createdAt = $createdAt;
        $this->pdo = Database::getInstance()->getConnection();
    }
       
    
    /* ================= GETTERS / SETTERS ================== */
    public function getId(): ?int { 
        return $this->id; 
    }
    
    public function setId(?int $id): void { 
        $this->id = $id; 
    }

    public function getUsername(): ?string { 
        return $this->username; 
    }
    
    public function setUsername(?string $username): void { 
        $this->username = $username; 
    }

    public function getEmail(): ?string { 
        return $this->email; 
    }
    
    public function setEmail(?string $email): void { 
        $this->email = $email; 
    }

    public function getPassword(): ?string { 
        return $this->password; 
    }
    
    public function setPassword(?string $password): void { 
        $this->password = $password; 
    }

    public function getProfilePhoto(): ?string { 
        return $this->profilePhoto; 
    }
    
    public function setProfilePhoto(?string $profilePhoto): void { 
        $this->profilePhoto = $profilePhoto; 
    }

    public function getCreatedAt(): ?DateTime { 
        return $this->createdAt; 
    }
    
    public function setCreatedAt(?DateTime $createdAt): void { 
        $this->createdAt = $createdAt; 
    }
}
?>