<?php

class User
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $password;

    # Méthodes
    public function __construct(array $data)
    {
        $this->hydrate($data); // Hydrate l'objet avec les données fournies
    }

    public function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            $method = "set" . ucfirst($key); // setId, setNumber, setName, etc.
            if (method_exists($this, $method)) {
                $this->$method($value); // Appelle la méthode setter correspondante
            }
        }
    }

    // Get the value of id
    public function getId(): int
    {
        return $this->id;
    }

    // Set the value of id
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    // Get the value of firstName
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    // Set the value of firstName
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    // Get the value of lastName
    public function getLastName(): string
    {
        return $this->lastName;
    }

    // Set the value of lastName

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    // Get the value of email

    public function getEmail(): string
    {
        return $this->email;
    }

    // Set the value of email
 
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    // Get the value of password

    public function getPassword(): string
    {
        return $this->password;
    }

    // Set the value of password
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}