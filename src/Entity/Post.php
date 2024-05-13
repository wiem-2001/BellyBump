<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;


    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdat = null;



    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $auteur = null;

    #[ORM\OneToMany(mappedBy: 'Post', targetEntity: LikeDislike::class, orphanRemoval: true)]
    private Collection $likeDislikes;



    public function __construct()
    {
        $this->createdat = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->likeDislikes = new ArrayCollection();


    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }





    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedat(): ?\DateTimeImmutable
    {
        return $this->createdat;
    }

    public function setCreatedat(\DateTimeImmutable $createdat): static
    {
        $this->createdat = $createdat;

        return $this;
    }

    public function __toString()
    {
        return(string)$this->getTitle();
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function getAuteur(): ?User
    {
        return $this->auteur;
    }

    public function setAuteur(?User $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * @return Collection<int, LikeDislike>
     */
    public function getLikeDislikes(): Collection
    {
        return $this->likeDislikes;
    }
    public function getLikes(): Collection
    {
        return $this->likeDislikes->filter(function($like) { return $like->isValue() === true; });

    }
    public function getDislikes(): Collection
    {
        return $this->likeDislikes->filter(function($like) { return $like->isValue() === false; });

    }


    public function addLikeDislike(LikeDislike $likeDislike): static
    {
        if (!$this->likeDislikes->contains($likeDislike)) {
            $this->likeDislikes->add($likeDislike);
            $likeDislike->setPost($this);
        }

        return $this;
    }

    public function removeLikeDislike(LikeDislike $likeDislike): static
    {
        if ($this->likeDislikes->removeElement($likeDislike)) {
            // set the owning side to null (unless already changed)
            if ($likeDislike->getPost() === $this) {
                $likeDislike->setPost(null);
            }
        }

        return $this;
    }


}
