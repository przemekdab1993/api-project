<?php


namespace App\Dto;

use App\Entity\UserApi;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

class CheeseListingInput
{
    #[Groups(['cheese:write', 'user-api:write'])]
    public ?string $title;

    #[Groups(['cheese:write', 'user-api:write'])]
    public ?int $price;

    #[Groups(['cheese:write'])]
    public ?bool $isPublished = false;

    #[Groups(['cheese:write', 'user-api:write'])]
    public ?int $quantity;

    #[Groups(['cheese:collection:post'])]
    public UserApi $owner;

    public string $description;

    #[Groups(['cheese:write', 'user-api:write'])]
    #[SerializedName('description')]
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }
}