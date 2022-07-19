<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    private static $instance = null;

    private function __construct() { }

    private function  __clone() { }

    public static function getInstance()
  {
    if (self::$instance == null)
    {
      self::$instance = new Article();
    }
 
    return self::$instance;
  }


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $date_debut;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $date_fin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $societe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numnat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $directeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $poste;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sexe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }


    public function getNumnat(): ?string
    {
        return $this->numnat;
    }

    public function setNumnat(string $numnat): self
    {
        $this->numnat = $numnat;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }


    public function getSociete(): ?string
    {
        return $this->societe;
    }

    public function setSociete(string $societe): self
    {
        $this->societe = $societe;

        return $this;
    }

    
    public function getDirecteur(): ?string
    {
        return $this->directeur;
    }

    public function setDirecteur(string $directeur): self
    {
        $this->directeur = $directeur;

        return $this;
    }


    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): self
    {
        $this->poste = $poste;

        return $this;
    }



    public function getDateDebut(): ?string
    {
        return $this->date_debut;
    }

    public function setDateDebut(string $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }


    public function getDateFin(): ?string
    {
        return $this->date_fin;
    }

    public function setDateFin(string $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }
}