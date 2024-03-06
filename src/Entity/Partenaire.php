<?php

namespace App\Entity;

use App\Repository\PartenaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DisconnectedClassMetadataFactory;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PartenaireRepository::class)]
class Partenaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The name must not be empty")
     */



    #[ORM\Column(length: 255)]
    private ?string $nom = null;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The brand must not be empty")
     */
    #[ORM\Column(length: 255)]
    private ?string $marque = null;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The email must not be empty")
     * @Assert\Email(message="The email '{{ value }}' is not a valid email")
     */
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="The description must not be empty")
     * @Assert\Length(
     *      min = 5,
     *      minMessage="The description must contain at least 5 characters"
     * )
     */
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'partenaire', targetEntity: Produit::class, orphanRemoval: true)]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setPartenaire($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getPartenaire() === $this) {
                $produit->setPartenaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
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

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;

    }
        public function getEmail(): ?string
    {
        return $this->email;
    }

        public function setEmail(string $email): static
    {
        $this->email = $email;

        return  $this;
    }


    public function setDescription(string $description): static
    {
        $this->description= $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }







}
