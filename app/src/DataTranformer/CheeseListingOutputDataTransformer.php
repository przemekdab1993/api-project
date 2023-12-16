<?php


namespace App\DataTranformer;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\CheeseListingInput;
use App\Dto\CheeseListingOutput;
use App\Entity\CheeseListing;

class CheeseListingOutputDataTransformer implements DataTransformerInterface
{

    /**
     * @param CheeseListing $cheeseListing
     */
    public function transform($cheeseListing, string $to, array $context = []): CheeseListingOutput
    {
        $output = new CheeseListingOutput();
        $output->title = $cheeseListing?->getTitle();
        $output->description = $cheeseListing?->getDescription();
        $output->price = $cheeseListing?->getPrice();
        $output->quantity = $cheeseListing?->getQuantity();
        $output->createdAt = $cheeseListing?->getCreatedAt();
        $output->owner = $cheeseListing?->getOwner();

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return  $data instanceof CheeseListing && $to === CheeseListingInput::class;
    }
}