<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\UserApiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: UserApiRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'access_control' => 'is_granted("ROLE_USER")'
        ],
        'post' => [
            'access_control' => 'is_granted("IS_AUTHENTICATED_ANONYMOUSLY")',
            'validation_groups' => ['Default', 'create']
        ]
    ],
    itemOperations: [
        'get' => [
            'access_control' => 'is_granted("ROLE_USER")',
        ],
        'put' => [
            'access_control' => 'is_granted("ROLE_USER") and object == user',
        ],
        'delete' => [
            'access_control' => 'is_granted("ROLE_ADMIN")',
        ]
    ],
    shortName: 'user_api',
    denormalizationContext: ['groups' => ['user_api:write']],
    normalizationContext: ['groups' => ['user_api:read']]

)]
#[ApiFilter(PropertyFilter::class)]
#[UniqueEntity(fields: ['userName'])]
#[UniqueEntity(fields: ['email'])]
#[ORM\EntityListeners(['App\Doctrine\UserSetIsMvpListener'])]
class UserApi implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Email]
    #[NotBlank]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['user_api:read', 'user_api:write', 'cheese:item:get'])]
    private string $email;

    #[ORM\Column(type: 'json')]
    #[Groups(['admin:write'])]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[Groups(['user_api:write'])]
    #[NotBlank(groups: ['create'])]
    private $plainPassword;

    #[NotBlank]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups([
        'user_api:read', 'user_api:write',
        'cheese:item:get',
        'owner:read'
    ])]
    private ?string $userName;

    #[Valid]
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: CheeseListing::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['user_api:write'])]
    private Collection $cheeseListings;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['admin:read', 'owner:read', 'user_api:write'])]
    private ?string $phoneNumber;

    #[Groups(['user_api:read'])]
    private bool $isMe = false;

    #[Groups(['user_api:read'])]
    private bool $isMvp = false;



    public function __construct()
    {
        $this->cheeseListings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUserName(): string
    {
        return (string) $this->userName;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    #[SerializedName('password')]
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
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
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    /**
     * @return Collection<int, CheeseListing>
     */
    public function getCheeseListings(): Collection
    {
        return $this->cheeseListings;
    }

    /**
     * @return Collection<int, CheeseListing>
     */
    #[Groups(['user_api:read'])]
    #[SerializedName('cheeseListings')]
    public function getPublishedCheeseListings(): Collection
    {
        return $this->cheeseListings->filter(function (CheeseListing $cheeseListing) {
            return $cheeseListing->getIsPublished();
        });
    }

    public function addCheeseListing(CheeseListing $cheeseListing): self
    {
        if (!$this->cheeseListings->contains($cheeseListing)) {
            $this->cheeseListings[] = $cheeseListing;
            $cheeseListing->setOwner($this);
        }

        return $this;
    }

    public function removeCheeseListing(CheeseListing $cheeseListing): self
    {
        if ($this->cheeseListings->removeElement($cheeseListing)) {
            // set the owning side to null (unless already changed)
            if ($cheeseListing->getOwner() === $this) {
                $cheeseListing->setOwner(null);
            }
        }

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getIsMe(): bool
    {
        return $this->isMe;
    }

    public function setIsMe(bool $isMe): void
    {
        $this->isMe = $isMe;
    }

    public function getIsMvp(): bool
    {
        return $this->isMvp;
    }

    public function setIsMvp(bool $isMvp): void
    {
        $this->isMvp = $isMvp;
    }
}
