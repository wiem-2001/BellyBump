<?php

namespace App\Entity;

use App\Repository\InfoMedicauxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InfoMedicauxRepository::class)]
class InfoMedicaux
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 15)]
    private ?string $maladie = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Range(
        min: 0,
        max: 10,
        notInRangeMessage: "Le nombre de vaccins doit être compris entre 0 et 10."
    )]    private ?int $nbrVaccin = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $dateVaccin = null;

    #[ORM\Column(length: 255)]
    private ?string $BloodType = null;

    
    #[ORM\Column(length: 255)]
    private ?string $sicknessEstimation = null;

    #[ORM\OneToOne(mappedBy: 'infoMedicaux', targetEntity: Med::class, cascade: ['persist', 'remove'])]
    private ?Med $med = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Baby $babyName = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaladie(): ?string
    {
        return $this->maladie;
    }

    public function setMaladie(string $maladie): static
    {
        $this->maladie = $maladie;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getNbrVaccin(): ?int
    {
        return $this->nbrVaccin;
    }

    public function setNbrVaccin(int $nbrVaccin): static
    {
        $this->nbrVaccin = $nbrVaccin;

        return $this;
    }

    public function getDateVaccin(): ?\DateTimeInterface
    {
        return $this->dateVaccin;
    }

    public function setDateVaccin(\DateTimeInterface  $dateVaccin): static
    {
        $this->dateVaccin = $dateVaccin;

        return $this;
    }

    public function getBloodType(): ?string
    {
        return $this->BloodType;
    }

    public function setBloodType(string $BloodType): static
    {
        $this->BloodType = $BloodType;

        return $this;
    }

    public function getSicknessEstimation(): ?string
    {
        return $this->sicknessEstimation;
    }

    public function setSicknessEstimation(string $sicknessEstimation): static
    {
        $this->sicknessEstimation = $sicknessEstimation;

        return $this;
    }

    public function getMed(): ?Med
    {
        return $this->med;
    }

    public function setMed(?Med $med): static
    {
        $this->med = $med;

        return $this;
    }

    public function getBabyName(): ?Baby
    {
        return $this->babyName;
    }

    public function setBabyName(?Baby $babyName): static
    {
        $this->babyName = $babyName;

        return $this;
    }
}
