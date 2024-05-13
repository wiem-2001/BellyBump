<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
#[UniqueEntity(fields: ['DateReservation', 'heureReservation','nomMed'], message: 'Un rendez-vous existe déjà à cette date et heure.')]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

     
   
   
   
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateReservation = null;

    #[ORM\Column]
    private ?int $heureReservation = null;

    #[ORM\ManyToOne(inversedBy: 'RendezVous')]
    private ?Med $nomMed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->DateReservation;
    }

    public function setDateReservation(\DateTimeInterface $DateReservation): static
    {
        $this->DateReservation = $DateReservation;

        return $this;
    }

    public function getHeureReservation(): ?int
    {
        return $this->heureReservation;
    }

    public function setHeureReservation(int $heureReservation): static
    {
        $this->heureReservation = $heureReservation;

        return $this;
    }

    public function getNomMed(): ?Med
    {
        return $this->nomMed;
    }

    public function setNomMed(?Med $nomMed): static
    {
        $this->nomMed = $nomMed;

        return $this;
    }
}
