<?php

namespace App\Entity;

use App\Repository\BabyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

#[ORM\Entity(repositoryClass: BabyRepository::class)]
class Baby
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 15)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $sexe = null;
    

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    #[CustomAssert\AfterToday]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    private ?float $poids = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    private ?float $taille = null;

    #[ORM\OneToMany(mappedBy: 'baby', targetEntity: InfoMedicaux::class)]
    private Collection $IdInfoMedicaux;

    public function __construct()
    {
        $this->IdInfoMedicaux = new ArrayCollection();
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

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(float $poids): static
    {
        $this->poids = $poids;

        return $this;
    }

    public function getTaille(): ?float
    {
        return $this->taille;
    }

    public function setTaille(float $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    /**
     * @return Collection<int, InfoMedicaux>
     */
    public function getIdInfoMedicaux(): Collection
    {
        return $this->IdInfoMedicaux;
    }

    public function addIdInfoMedicaux(InfoMedicaux $idInfoMedicaux): static
    {
        if (!$this->IdInfoMedicaux->contains($idInfoMedicaux)) {
            $this->IdInfoMedicaux->add($idInfoMedicaux);
            $idInfoMedicaux->setBaby($this);
        }

        return $this;
    }

    public function removeIdInfoMedicaux(InfoMedicaux $idInfoMedicaux): static
    {
        if ($this->IdInfoMedicaux->removeElement($idInfoMedicaux)) {
            // set the owning side to null (unless already changed)
            if ($idInfoMedicaux->getBaby() === $this) {
                $idInfoMedicaux->setBaby(null);
            }
        }

        return $this;
    }
}
