<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\BookRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    collectionOperations: ["get", "post"],
    itemOperations: [
        "get" => [
            "normalization_context" => [
                "groups"=> [
                    "book:read",
                    "book:item:get"
                ]
            ]
        ],
        "put"
    ],
    attributes: [
        "pagination_items_per_page" => 3,
        "formats" => [
            "jsonld",
            "json",
            "csv" => ["text/csv"]]
        ],
    denormalizationContext: ["groups"=>["book:write"]],
    normalizationContext: ["groups"=>["book:read"]]

)]
#[ApiFilter(BooleanFilter::class, properties: ["isPublished"])]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        "title"=>"partial",
        "author"=>"exact",
        "author.firstName"=>"partial"
    ]
)]
#[ApiFilter(RangeFilter::class, properties: ["price"])]
#[ApiFilter(PropertyFilter::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        "book:read",
        "book:write",
        "author:read",
        "author:write"
    ])]
    #[NotBlank]
    #[Length(
        min: 2,
        max: 40,
        maxMessage: "Describe your book in 40 chars or less."
    )]
    private string $title;


    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups([
        "book:read",
        "author:read",
        "author:write"
    ])]
    #[NotBlank]
    private string $description;


    #[ORM\Column(type: 'integer')]
    #[Groups([
        "book:read",
        "book:write",
        "author:write"
    ])]
    #[NotBlank]
    private int $price;


    #[ORM\Column(type: 'datetime')]
    private $createdAt;


    #[ORM\Column(type: 'boolean')]
    #[Groups(["book:read"])]
    private $isPublished = false;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        "book:read",
        "book:write",
        "author:write"
    ])]
    #[NotBlank]
    private int $quantity;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        "book:read",
        "book:write",
        "author:write"
    ])]
    #[Valid]
    private $author;

    public function __construct(string $title = null)
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->title = $title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

//    public function setTitle(string $title): self
//    {
//        $this->title = $title;
//
//        return $this;
//    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[Groups(["book:read"])]
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 40) {
            return $this->description;
        }
        return substr($this->description, 0,40).'...';

    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
    #[Groups(["book:write", "author:write"])]
    #[SerializedName("description")]
    public function setTextDescription(?string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    #[Groups(["book:read"])]
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }


    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

//    public function setIsPublished(bool $isPublished): self
//    {
//        $this->isPublished = $isPublished;
//
//        return $this;
//    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }
}
