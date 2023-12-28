<?php


namespace App\Dto;

use App\Entity\UserApi;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

class CheeseListingInput
{
    #[Groups(['cheese:write', 'user-api:write'])]
    public ?string $title = null;

    #[Groups(['cheese:write', 'user-api:write'])]
    public ?int $price = 0;

    #[Groups(['cheese:write'])]
    public ?bool $isPublished = false;

    #[Groups(['cheese:write', 'user-api:write'])]
    public ?int $quantity = 0;

    #[Groups(['cheese:collection:post'])]
    public ?UserApi $owner = null;

    public ?string $description = null;

    #[Groups(['cheese:write', 'user-api:write'])]
    #[SerializedName('description')]
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }
}