<?php

namespace App\Entity;

use App\Repository\EtabRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtabRepository::class)]
class Etab
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;


#[ORM\Column(length: 255)]
    private ?string $nom = null;

    
    

    #[ORM\Column(length: 255)]
    private ?string $localisation = null;

    #[ORM\OneToMany(mappedBy: 'etab', targetEntity: Med::class)]
    private Collection $Med;

    public function __construct()
    {
        $this->Med = new ArrayCollection();
    }

    // #[ORM\ManyToOne(inversedBy: 'etabs')]
    // private ?Med $Med = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
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

   

   

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }
    /**
     * @return Collection<int, Med>
     */
    public function getMed(): Collection
    {
        return $this->Med;
    }

    public function addMed(Med $med): static
    {
        if (!$this->Med->contains($med)) {
            $this->Med->add($med);
            $med->setEtab($this);
        }

        return $this;
    }

    public function removeMed(Med $med): static
    {
        if ($this->Med->removeElement($med)) {
            // set the owning side to null (unless already changed)
            if ($med->getEtab() === $this) {
                $med->setEtab(null);
            }
        }

        return $this;
    }

   

   
}
