<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'category:item']),
        new GetCollection(),
        new Post(
            normalizationContext: ['groups' => 'category:item'],
            denormalizationContext: ['groups' => 'category:write'],
        ),
        new Delete(
            normalizationContext: ['groups' => 'category:item']
        ),
        new Put(
            normalizationContext: ['groups' => 'category:item'],
            denormalizationContext: ['groups' => 'category:write']
        ),
    ],
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:item', 'category:write', 'comment:item', 'comment:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, options: ['default' => 'uncategorized'])]
    #[Groups(['category:item', 'category:write', 'comment:item', 'comment:write'])]
    #[Assert\Length(
        min: 5,
        max: 225,
        minMessage: 'CategoryName must be at least {{ limit }} characters long',
        maxMessage: 'CategoryName cannot be longer than {{ limit }} characters',
    )]
    private ?string $name = 'uncategorized';

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['category:item', 'category:write', 'comment:item',  'comment:write'])]
    private ?string $description = "What is this category?";

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Comment::class)]
    #[Groups(['category:item', 'category:write'])]
    private ?Collection $comments = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }
    public function __toString(): string
    {
        //EasyAdmin:filter
        return $this->id . '://' . $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $comment->setCategory($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCategory() === $this) {
                $comment->setCategory(null);
            }
        }

        return $this;
    }
}
