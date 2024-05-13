<?php

namespace App\Entity;

use App\Repository\CoachRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CoachRepository::class)]
#[UniqueEntity(fields: 'email', message: "This email is already in use")]
class Coach
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $firstname = null;

    #[ORM\Column(length: 30)]
    private ?string $lastname = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $job = null;

    #[ORM\Column(nullable: true)]
    private ?int $phone = null;

    #[ORM\OneToMany(mappedBy: 'coach', targetEntity: Event::class)]
    private Collection $cochedEvents;

    #[ORM\Column(length: 50)]
    private ?string $email = null;

    public function __construct()
    {
        $this->cochedEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $name): static
    {
        $this->firstname = $name;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getCochedEvents(): Collection
    {
        return $this->cochedEvents;
    }

    public function addCochedEvent(Event $cochedEvent): static
    {
        if (!$this->cochedEvents->contains($cochedEvent)) {
            $this->cochedEvents->add($cochedEvent);
            $cochedEvent->setCoach($this);
        }

        return $this;
    }

    public function removeCochedEvent(Event $cochedEvent): static
    {
        if ($this->cochedEvents->removeElement($cochedEvent)) {
            // set the owning side to null (unless already changed)
            if ($cochedEvent->getCoach() === $this) {
                $cochedEvent->setCoach(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function __toString()
    {
        return $this->getFirstname()."  ".$this->getLastname();
    }
}
