<?php


namespace App\DataTranformer;


use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use App\Dto\CheeseListingInput;
use App\Dto\CheeseListingOutput;
use App\Entity\CheeseListing;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CheeseListingInputDataTransformer implements DataTransformerInitializerInterface
{
    /**
     * @param CheeseListingInput $input
     */
    public function transform($input, string $to, array $context = []): CheeseListing
    {
        $cheeseListing = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;

        return $input->createOrUpdateEntity($cheeseListing);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof CheeseListing) {

            // already transformed
            return false;
        }

        return ($to === CheeseListing::class) && (($context['input']['class'] ?? null) === CheeseListingInput::class);
    }

    public function initialize(string $inputClass, array $context = []): CheeseListingInput
    {
        $dto = new CheeseListingInput();

        if (isset($context[AbstractItemNormalizer::OBJECT_TO_POPULATE])) {

            /** @var CheeseListing $cheeseListing */
            $cheeseListing = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE];

            $dto->title = $cheeseListing->getTitle();
            $dto->description = $cheeseListing->getDescription();
            $dto->price = $cheeseListing->getPrice();
            $dto->isPublished = $cheeseListing->getIsPublished();
            $dto->owner = $cheeseListing->getOwner();
        }

        return $dto;
    }
}