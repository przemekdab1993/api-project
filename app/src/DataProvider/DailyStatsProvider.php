<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\DailyStats;

class DailyStatsProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $stats = new DailyStats(new \DateTimeImmutable(), 100, []);

        return [$stats];
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === DailyStats::class;
    }
}