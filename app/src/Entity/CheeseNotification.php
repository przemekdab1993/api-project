<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CheeseNotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CheeseNotificationRepository::class)]
#[ApiResource]
class CheeseNotification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: CheeseListing::class, inversedBy: 'cheeseNotifications', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private CheeseListing $cheeseListing;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $notificationText;

    public function __construct(CheeseListing $cheeseListing, string $notificationText) {
        $this->cheeseListing = $cheeseListing;
        $this->notificationText = $notificationText;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheeseListing(): ?CheeseListing
    {
        return $this->cheeseListing;
    }

    public function setCheeseListing(?CheeseListing $cheeseListing): self
    {
        $this->cheeseListing = $cheeseListing;

        return $this;
    }

    public function getNotificationText(): ?string
    {
        return $this->notificationText;
    }

    public function setNotificationText(?string $notificationText): self
    {
        $this->notificationText = $notificationText;

        return $this;
    }
}
