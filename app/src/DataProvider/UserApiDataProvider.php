<?php


namespace App\DataProvider;


use ApiPlatform\Core\Bridge\Doctrine\Orm\CollectionDataProvider;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\UserApi;
use App\Repository\UserApiRepository;
use Symfony\Component\Security\Core\Security;

class UserApiDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface, DenormalizedIdentifiersAwareItemDataProviderInterface
{

    public function __construct(
        private CollectionDataProvider $collectionDataProvider,
        private ItemDataProviderInterface $itemDataProvider,
        private Security $security
    ) {
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        /**
         * @var UserApi[] $users
         */
        $users = $this->collectionDataProvider->getCollection($resourceClass, $operationName = null, $context);

        $currentUser = $this->security->getUser();

        foreach ($users as $user) {
            $user->setIsMe($user === $currentUser);
        }
        return $users;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === UserApi::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var UserApi|null $item */
        $item =  $this->itemDataProvider->getItem($resourceClass, $id, $operationName, $context);

        if (!$item) {
           return null;
        }

        $item->setIsMe($this->security->getUser() === $item);

        return $item;
    }
}