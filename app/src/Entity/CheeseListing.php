<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\ApiPlatform\CheeseListingSearchFilter;
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
use Symfony\Component\Validator\Constraints\Valid;

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
            'normalization_context' => [
                'groups'=> ['cheese:read', 'cheese:item:get']
            ]
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
    normalizationContext: ['groups' => ['cheese:read']]
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
#[ApiFilter(CheeseListingSearchFilter::class)]
#[ORM\Entity(repositoryClass: CheeseListingRepository::class)]
#[ORM\EntityListeners(['App\Doctrine\CheeseListingSetOwnerListener'])]
#[ValidIsPublished]
class CheeseListing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('cheese:read')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['cheese:read', 'cheese:write', 'user-api:read', 'user-api:write'])]
    #[NotBlank]
    #[Length(
        min: 2,
        max: 50,
        maxMessage: 'Describe your cheese in 50 chars or less'
    )]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['cheese:read', 'user-api:read'])]
    #[NotBlank]
    private $description;

    #[ORM\Column(type: 'integer')]
    #[Groups(['cheese:read', 'cheese:write', 'user-api:read', 'user-api:write'])]
    #[NotBlank]
    private $price;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['cheese:write'])]
    private $isPublished = false;

    #[ORM\Column(type: 'integer')]
    #[Groups(['cheese:read', 'cheese:write', 'user-api:read', 'user-api:write'])]
    #[NotBlank]
    private $quantity;

    #[ORM\ManyToOne(targetEntity: UserApi::class, inversedBy: 'cheeseListings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cheese:read', 'cheese:collection:post'])]
    #[IsValidOwner]
    //#[NotBlank]
    private $owner;

    #[ORM\OneToMany(mappedBy: 'cheeseListing', targetEntity: CheeseNotification::class)]
    private $cheeseNotifications;


    public function __construct(string $title = null)
    {
        $this->createdAt = new \DateTimeImmutable();
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

    #[Groups('cheese:read')]
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 40) {
            return $this->description;
        }

        return substr($this->description, 0, 40).'...';
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }


    #[Groups('cheese:read')]
    public function getCreatedAgo(): string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
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

    public function setOwner(?UserApi $owner): self
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
