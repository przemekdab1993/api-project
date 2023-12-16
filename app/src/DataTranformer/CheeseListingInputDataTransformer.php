<?php


namespace App\DataTranformer;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\CheeseListingInput;
use App\Entity\CheeseListing;

class CheeseListingInputDataTransformer implements DataTransformerInterface
{
    /**
     * @param CheeseListingInput $input
     */
    public function transform($input, string $to, array $context = []): CheeseListing
    {
        $cheeseListing = new CheeseListing();
        $cheeseListing->setTitle($input?->title);
        $cheeseListing->setDescription($input?->description);
        $cheeseListing->setPrice($input?->price);
        $cheeseListing->setIsPublished($input?->isPublished);
        $cheeseListing->setQuantity($input?->quantity);
        $cheeseListing->setOwner($input?->owner);

        return $cheeseListing;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof CheeseListing) {

            // already transformed
            return false;
        }

        return ($to === CheeseListing::class) && (($context['input']['class'] ?? null) === CheeseListingInput::class);
    }
}