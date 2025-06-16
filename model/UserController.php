<?php

    class UserController{
        private PDO $db;

        public function __construct() // Constructeur de la classe UserController
        {
            try {
                $this->db = new PDO("mysql:host=localhost;dbname=clash_deck;port=3306;charset=utf8mb4", "secure_user", "he93ts28&*"); // Changez les paramètres de connexion selon votre configuration
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Affiche les erreurs PDO
            } catch (PDOException $error) { // Si la connexion échoue, on affiche un message d'erreur
                echo "<p style='color:red'>{$error->getMessage()}</p>"; // Affiche l'erreur de connexion
            }
        }

        public function createUser(User $user): bool // Méthode pour créer un nouvel utilisateur
        {
            try {
                $req = $this->db->prepare("INSERT INTO `users` (firstName, lastName, email, password) VALUES (:firstName, :lastName, :email, :password)"); // Prépare la requête d'insertion

                $req->bindValue(":firstName", $user->getFirstName(), PDO::PARAM_STR); // Lie les valeurs aux paramètres de la requête
                $req->bindValue(":lastName", $user->getLastName(), PDO::PARAM_STR); // Lie les valeurs aux paramètres de la requête
                $req->bindValue(":email", $user->getEmail(), PDO::PARAM_STR); // Lie les valeurs aux paramètres de la requête
                $req->bindValue(":password", $user->getPassword(), PDO::PARAM_STR); // Lie les valeurs aux paramètres de la requête

                return $req->execute(); // Exécute la requête et retourne le résultat
            } catch (PDOException $e) { // Si une exception est levée, on la capture
                return false; // Retourne false en cas d'erreur
            }
        }

        public function updateUser(User $user): bool // Méthode pour mettre à jour un utilisateur existant
        {
            try {
                $req = $this->db->prepare("UPDATE `users` SET firstName = :firstName, lastName = :lastName, email = :email, password = :password WHERE id = :id"); // Prépare la requête de mise à jour
                
                $req->bindValue(":firstName", $user->getFirstName(), PDO::PARAM_STR); // Lie les valeurs aux paramètres de la requête
                $req->bindValue(":lastName", $user->getLastName(), PDO::PARAM_STR); // Lie les valeurs aux paramètres de la requête
                $req->bindValue(":email", $user->getEmail(), PDO::PARAM_STR); // Lie les valeurs aux paramètres de la requête
                $req->bindValue(":password", $user->getPassword(), PDO::PARAM_STR); // Lie les valeurs aux paramètres de la requête
                $req->bindValue(":id", $user->getId(), PDO::PARAM_INT); // Lie l'ID de l'utilisateur à mettre à jour
                
                return $req->execute(); //  exécute la requête et retourne le résultat
            } catch (PDOException $e) { // Si une exception est levée, on la capture
                return false; // Retourne false en cas d'erreur
            }
        }

        public function deleteUser(int $id): bool // Méthode pour supprimer un utilisateur par son ID
        {
            try {
                $req = $this->db->prepare("DELETE FROM `users` WHERE id=:id"); // Prépare la requête de suppression
                $req->bindValue(":id", $id, PDO::PARAM_INT); // Lie l'ID de l'utilisateur à supprimer
                return $req->execute(); // Exécute la requête et retourne le résultat
            } catch (PDOException $e) { // Si une exception est levée, on la capture
                return false; // Retourne false en cas d'erreur
            }
        }

        public function readUser(int $id): ?User // Méthode pour lire un utilisateur par son ID
        {
            $req = $this->db->prepare("SELECT * FROM `users` WHERE id = :id"); // Prépare la requête de sélection
            $req->bindValue(":id", $id, PDO::PARAM_INT); // Lie l'ID de l'utilisateur à lire
            $req->execute(); // Exécute la requête
            $data = $req->fetch(); // Récupère les données de l'utilisateur

            if (!$data) {
                return null; // Si aucune donnée n'est trouvée, retourne null
            }
            return new User($data); // Retourne un nouvel objet User avec les données récupérées
        }

        public function readAllUser(): array // Méthode pour lire tous les utilisateurs
        {
            $users = []; // Tableau pour stocker les utilisateurs
            $req = $this->db->prepare("SELECT * FROM `users` ORDER BY id ASC"); // Prépare la requête de sélection de tous les utilisateurs
            $req->execute(); // Exécute la requête
            $datas = $req->fetchAll(); // Récupère toutes les données des utilisateurs
            foreach ($datas as $data) { // Pour chaque donnée récupérée
                $users[] = new User($data); // Crée un nouvel objet User et l'ajoute au tableau $users
            }
            return $users; // Retourne le tableau des utilisateurs
        }

        public function getUserByEmail(string $email): ?User // Méthode pour récupérer un utilisateur par son email
        {
            $req = $this->db->prepare("SELECT * FROM `users` WHERE email = :email"); // Prépare la requête de sélection par email
            $req->bindValue(":email", $email, PDO::PARAM_STR); // Lie l'email à rechercher
            $req->execute(); // Exécute la requête
            $data = $req->fetch(); // Récupère les données de l'utilisateur
            if (!$data) { 
                return null; // Si aucune donnée n'est trouvée, retourne null
            }
            return new User($data); // Retourne un nouvel objet User avec les données récupérées
        }
    }

?>