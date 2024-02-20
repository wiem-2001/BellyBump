<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomMedcin = null;

    #[ORM\Column]
    private ?int $prix = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateReservation = null;

    #[ORM\Column]
    private ?int $heureReservation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomMedcin(): ?string
    {
        return $this->nomMedcin;
    }

    public function setNomMedcin(string $nomMedcin): static
    {
        $this->nomMedcin = $nomMedcin;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
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
}
