<?php


namespace App\Dto;

use App\Entity\UserApi;
use Carbon\Carbon;
use Symfony\Component\Serializer\Annotation\Groups;

class CheeseListingOutput
{
    #[Groups(['cheese:read', 'user-api:read'])]
    public ?string $title;

    #[Groups(['cheese:read'])]
    public ?string $description;

    #[Groups(['cheese:read', 'user-api:read'])]
    public ?int $price;

    #[Groups(['cheese:read', 'user-api:read'])]
    public ?int $quantity;

    #[Groups(['cheese:read'])]
    public ?UserApi $owner;

    public ?\DateTime $createdAt;

    #[Groups('cheese:read')]
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 40) {
            return $this->description;
        }

        return substr($this->description, 0, 40).'...';
    }

    #[Groups('cheese:read')]
    public function getCreatedAgo(): ?string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }
}