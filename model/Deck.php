<?php

class Deck {
    private int $id;
    private string $title;
    private string $description;
    private string $created_at;
    private ?int $author_id = null; // accepte null
    public function __construct(array $data = []) {
        $this->hydrate($data);
    }

    public function hydrate(array $data): void {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    // Getters
    public function getId(): int { 
        return $this->id; 
    }

    public function getTitle(): string { 
        return $this->title; 
    }

    public function getDescription(): string { 
        return $this->description; 
    }

    public function getCreatedAt(): string { 
        return $this->created_at; 
    }

    public function getAuthorId(): ?int {
        return $this->author_id;
    }

    // Setters
    public function setId($id): void { 
        $this->id = (int)$id; 
    }

    public function setTitle($title): void { 
        $this->title = (string)$title; 
    }

    public function setDescription($description): void { 
        $this->description = (string)$description; 
    }

    public function setCreated_at($created_at): void { 
        $this->created_at = $created_at; 
    }
    
    public function setAuthor_id($author_id): void { 
        $this->author_id = $author_id !== null ? (int)$author_id : null;
    }
}
