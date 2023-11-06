<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\DailyStats;
use App\Repository\CheeseListingRepository;
use App\Service\StatsHelper;

class DailyStatsProvider implements CollectionDataProviderInterface, ItemDataProviderInterface, RestrictedDataProviderInterface
{

    public function __construct(
        private StatsHelper $statsHelper
    ) {
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        return $this->statsHelper->fetchMany();
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === DailyStats::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        return $this->statsHelper->fetchOne($id);
    }
}