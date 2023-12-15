<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\ApiPlatform\CheeseListingSearchFilter;
use App\Dto\CheeseListingOutput;
use App\Validator\IsValidOwner;
use App\Validator\ValidIsPublished;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use App\Repository\CheeseListingRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    collectionOperations: [
        'get' => [
        ],
        'post' => [
            'access_control' => 'is_granted("ROLE_USER")'
        ]
    ],
    itemOperations: [
        'get' => [
        ],
        'put' => [
            'access_control' => 'is_granted("EDIT", previous_object)',
            'access_control_message' => 'Only the creator can edit a cheeseListing'
        ],
        'delete' => [
            'access_control' => 'is_granted("ROLE_ADMIN")'
        ]
    ],
    shortName: 'cheese',
    attributes: [
        'pagination_items_per_page' => 10,
        'formats' => [
            'jsonld', 'json', 'html', 'jsonhal', 'csv' => ['text/csv']
        ]
    ],
    denormalizationContext: ['groups' => ['cheese:write']],
    output: CheeseListingOutput::class
)]
#[ApiFilter(BooleanFilter::class, properties: ['isPublished'])]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'title'=>'partial',
        'owner'=>'exact',
        'owner.userName'=>'partial'
    ]
)]
#[ApiFilter(RangeFilter::class, properties: ['price'])]
#[ApiFilter(PropertyFilter::class)]
#[ApiFilter(CheeseListingSearchFilter::class, arguments: ["useLike" => true])]
#[ORM\Entity(repositoryClass: CheeseListingRepository::class)]
#[ORM\EntityListeners(['App\Doctrine\CheeseListingSetOwnerListener'])]
#[ValidIsPublished]
class CheeseListing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['cheese:write', 'user-api:write'])]
    #[NotBlank]
    #[Length(
        min: 2,
        max: 50,
        maxMessage: 'Describe your cheese in 50 chars or less'
    )]
    private ?string $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['user-api:read'])]
    #[NotBlank]
    private ?string $description;

    #[ORM\Column(type: 'integer')]
    #[Groups(['cheese:write', 'user-api:write'])]
    #[NotBlank]
    private ?int $price;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['cheese:write'])]
    private ?bool $isPublished = false;

    #[ORM\Column(type: 'integer')]
    #[Groups(['cheese:write', 'user-api:write'])]
    #[NotBlank]
    private ?int $quantity;

    #[ORM\ManyToOne(targetEntity: UserApi::class, inversedBy: 'cheeseListings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cheese:collection:post'])]
    #[IsValidOwner]
    private $owner;

    #[ORM\OneToMany(mappedBy: 'cheeseListing', targetEntity: CheeseNotification::class)]
    private Collection $cheeseNotifications;


    public function __construct(string $title = null)
    {
        $this->createdAt = new \DateTime();
        $this->title = $title;
        $this->cheeseNotifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    #[Groups(['cheese:write', 'user-api:write'])]
    #[SerializedName('description')]
    public function setTextDescription(string $description): self
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

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOwner(): ?UserApi
    {
        return $this->owner;
    }

    public function setOwner(UserApi $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, CheeseNotification>
     */
    public function getCheeseNotifications(): Collection
    {
        return $this->cheeseNotifications;
    }

    public function addCheeseNotification(CheeseNotification $cheeseNotification): self
    {
        if (!$this->cheeseNotifications->contains($cheeseNotification)) {
            $this->cheeseNotifications[] = $cheeseNotification;
            $cheeseNotification->setCheeseListing($this);
        }

        return $this;
    }

    public function removeCheeseNotification(CheeseNotification $cheeseNotification): self
    {
        if ($this->cheeseNotifications->removeElement($cheeseNotification)) {
            // set the owning side to null (unless already changed)
            if ($cheeseNotification->getCheeseListing() === $this) {
                $cheeseNotification->setCheeseListing(null);
            }
        }

        return $this;
    }
}
