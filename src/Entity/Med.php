<?php

namespace App\Entity;

use App\Repository\MedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?Etab $etab = null;

    #[ORM\OneToMany(mappedBy: 'nomMed', targetEntity: RendezVous::class)]
    private Collection $RendezVous;

    public function __construct()
    {
        $this->RendezVous = new ArrayCollection();
    }

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

    public function getEtab(): ?Etab
    {
        return $this->etab;
    }

    public function setEtab(?Etab $etab): static
    {
        $this->etab = $etab;

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVous(): Collection
    {
        return $this->RendezVous;
    }

    public function addRendezVou(RendezVous $rendezVou): static
    {
        if (!$this->RendezVous->contains($rendezVou)) {
            $this->RendezVous->add($rendezVou);
            $rendezVou->setNomMed($this);
        }

        return $this;
    }

    public function removeRendezVou(RendezVous $rendezVou): static
    {
        if ($this->RendezVous->removeElement($rendezVou)) {
            // set the owning side to null (unless already changed)
            if ($rendezVou->getNomMed() === $this) {
                $rendezVou->setNomMed(null);
            }
        }

        return $this;
    }
}

