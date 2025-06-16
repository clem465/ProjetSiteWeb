<?php

class NewsController
{
    private $articles; // Stocke les articles

    public function __construct()
    {
        $jsonPath = __DIR__ . '/../data/articles.json';

        if (file_exists($jsonPath)) { // Vérifie si le fichier JSON existe
            $json = file_get_contents($jsonPath); // Lit le contenu du fichier JSON
            $this->articles = json_decode($json, true); // Décode le JSON en tableau associatif
        } else {
            $this->articles = [];
        }
    }

    public function getArticles()
    {
        return $this->articles;
    }
}
