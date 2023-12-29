<?php


namespace App\Dto;

use App\Entity\CheeseListing;
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

    public function createOrUpdateEntity(?CheeseListing $cheeseListing): CheeseListing
    {
        if (!$cheeseListing) {
            $cheeseListing = new CheeseListing();
        }

        $cheeseListing->setTitle($this->title);
        $cheeseListing->setDescription($this->description);
        $cheeseListing->setPrice($this->price);
        $cheeseListing->setIsPublished($this->isPublished);
        $cheeseListing->setQuantity($this->quantity);
        $cheeseListing->setOwner($this->owner);

        return $cheeseListing;
    }
    public static function createFromEntity(?CheeseListing $cheeseListing): self
    {
        $dto = new CheeseListingInput();
        // not an edit, so just return an empty DTO
        if (!$cheeseListing) {
            return $dto;
        }

        $dto->title = $cheeseListing->getTitle();
        $dto->price = $cheeseListing->getPrice();
        $dto->description = $cheeseListing->getDescription();
        $dto->owner = $cheeseListing->getOwner();
        $dto->isPublished = $cheeseListing->getIsPublished();

        return $dto;
    }
}