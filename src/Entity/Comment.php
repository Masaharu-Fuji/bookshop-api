<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Resolver\CommentCreateResolver;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'comment:item'],
        ),
        new GetCollection(
            security: "is_granted('ROLE_USER')",
        ),
        new Post(
            normalizationContext: ['groups' => 'comment:item'],
            denormalizationContext: ['groups' => 'comment:write'],
            security: "is_granted('ROLE_USER')",
        ),
        new Delete(),
        new Put(
            denormalizationContext: ['groups' => 'comment:write'],
        ),
        new Patch(
            denormalizationContext: ['groups' => 'comment:write'],
        )
    ],
    order: ['id' => 'DESC'],
    paginationEnabled: true,
    paginationItemsPerPage: 5,
    paginationType: 'page',
    paginationClientItemsPerPage: true,
)]
#[ApiResource(
    graphQlOperations: [
        new Mutation(
            name: 'createResolver',
            resolver: CommentCreateResolver::class,
            args: [
                "title" => [
                    "type" => "String!"
                ],
                "sentence" => [
                    "type" => "String!"
                ],
                "state" => [
                    "type" => "String"
                ],
                "writer" => [
                    "type" => "String"
                ],
                "category" => [
                    "type" => "String"
                ],

            ]
        )
    ],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'title' => 'iexact', //完全一致(大小区別なし)
        'writer.nickname' => 'partial', //部分一致(前後一致の条件なし)
        'category.name' => 'exact', //完全一致(大小区別あり)
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id',
    ],
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['comment:write', 'comment:item', 'category:item', 'writer:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    #[Groups(['comment:write', 'comment:item', 'category:item', 'writer:item'])]
    #[Assert\Length(
        min: 5,
        max: 80,
        minMessage: 'Title must be at least {{ limit }} characters long',
        maxMessage: 'Title cannot be longer than {{ limit }} characters',
    )]
    private ?string $title = "No_title";

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['comment:write', 'comment:item'])]
    private ?string $sentence = null;

    #[ORM\Column(options: ['default' => 'submitted'])]
    #[Groups(['comment:write', 'comment:item'])]
    #[Assert\Choice(['submitted', 'published'])]
    private ?string $state = 'submitted';

    #[Groups(['comment:write', 'comment:item'])]
    #[ORM\Column(
        nullable: true
    )]
    private ?\DateTimeImmutable $publishedAt = null;

    #[Groups(['coment:write', 'comment:item'])]
    #[ORM\Column(
        nullable: true
    )]
    private \DateTimeImmutable $createdAt;

    #[ORM\JoinColumn]
    #[ORM\ManyToOne(
        inversedBy: 'comments',
        cascade: ["persist"],
    )]
    #[Groups(['comment:write', 'comment:item'])]
    #[ApiProperty(
        readableLink: true,
        writableLink: false
    )]
    private ?Writer $writer;

    #[ORM\JoinColumn]
    #[ORM\ManyToOne(
        inversedBy: 'comments',
        cascade: ["persist"],
    )]
    #[Groups(['comment:write', 'comment:item'])]
    #[ApiProperty(
        readableLink: true,
        writableLink: false
    )]
    private ?Category $category = null;
    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return "{$this->id}"; //EasyAdmin:filter
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getSentence(): ?string
    {
        return $this->sentence;
    }

    public function setSentence(string $sentence): static
    {
        $this->sentence = $sentence;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getWriter(): ?Writer
    {
        return $this->writer;
    }

    public function setWriter(?Writer $writer): static
    {

        $this->writer = $writer;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }
}
