<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\WriterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Resolver\WriterCreateResolver;
use App\State\UserPasswordHasher;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: WriterRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(processor: UserPasswordHasher::class),
        new Put(processor: UserPasswordHasher::class),
        new Patch(processor: UserPasswordHasher::class),
        new Delete(),
    ],
    normalizationContext: ['groups' => 'writer:item'],
    denormalizationContext: ['groups' => 'writer:write'],
    paginationEnabled: true,
)]
#[ApiResource(
    graphQlOperations: [
        new Mutation(
            name: 'createResolver',
            resolver: WriterCreateResolver::class,
            args: [
                "nickname" => [
                    "type" => "String"
                ],
                "username" => [
                    "type" => "String!"
                ],
                "email" => [
                    "type" => "String!"
                ],
            ],
        ),
    ],
)]
#[UniqueEntity('email')]
class Writer implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['writer:write', 'writer:item', 'comment:item', 'comment:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    #[Groups(['writer:write', 'writer:item', 'comment:item', 'comment:write'])]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 4,
        max: 20,
        minMessage: 'Username must be at least {{ limit }} characters long',
        maxMessage: 'Username cannot be longer than {{ limit }} characters',
    )]
    private string $username = 'anonymous';

    #[ORM\Column(length: 20, options: ['default' => 'unknown'])]
    #[Groups(['writer:write', 'writer:item', 'comment:item', 'comment:write'])]
    #[Assert\Length(
        min: 5,
        max: 20,
        minMessage: 'Nickname must be at least {{ limit }} characters long',
        maxMessage: 'Nickname cannot be longer than {{ limit }} characters',
    )]
    private ?string $nickname = 'unknown';

    #[ORM\Column(length: 255)]
    #[Groups(['writer:write', 'writer:item', 'comment:item', 'comment:write'])]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Email cannot be longer than {{ limit }} characters',
    )]
    private ?string $email = "example@mail.com";

    #[ORM\OneToMany(
        mappedBy: 'writer',
        targetEntity: Comment::class,
        orphanRemoval: true,
    )]
    #[Groups(['writer:item'])]
    private ?Collection $comments = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password;

    #[Assert\NotBlank(groups: ['writer:write'])]
    #[Groups(['writer:write', 'writer:item', 'comment:item', 'comment:write'])]
    private ?string $plainPassword = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'writer', targetEntity: ApiToken::class)]
    private Collection $apiTokens;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->apiTokens = new ArrayCollection();
    }

    public function __toString()
    {
        return "$this->id:// $this->username";
    }

    #[Assert\Callback()]
    public function validateName(ExecutionContextInterface $context)
    {
        if (strpos($this->username, $this->nickname) !== false) {
            $context->buildViolation('ペンネームにユーザネームを含めることはできません。')->atPath('nickname')->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setWriter($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getWriter() === $this) {
                $comment->setWriter(null);
            }
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Returning a salt is only needed if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getRoles(): array
    {
        /* 追記 */
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): static
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
            $apiToken->setWriter($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): static
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getWriter() === $this) {
                $apiToken->setWriter(null);
            }
        }

        return $this;
    }
}
