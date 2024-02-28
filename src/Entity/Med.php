<?php
 // src/Entity/Med.php

namespace App\Entity;

use App\Repository\MedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MedRepository::class)]
class Med
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $specialite = null;

    #[ORM\ManyToOne(inversedBy: 'Med')]
    private ?InfoMedicaux $infoMedicaux = null;

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getInfoMedicaux(): ?InfoMedicaux
    {
        return $this->infoMedicaux;
    }

    public function setInfoMedicaux(?InfoMedicaux $infoMedicaux): static
    {
        $this->infoMedicaux = $infoMedicaux;

        return $this;
    }

  
}