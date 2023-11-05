<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\DailyStats;
use App\Repository\CheeseListingRepository;

class DailyStatsProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{

    public function __construct(
        private CheeseListingRepository $cheeseListingRepository
    ) {
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $listings = $this->cheeseListingRepository->findBy([], [], 5);

        $stats1 = new DailyStats(new \DateTimeImmutable(), 1000, $listings);
        $stats2 = new DailyStats(new \DateTimeImmutable('-1 days'), 2000, $listings);

        return [$stats1, $stats2];
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === DailyStats::class;
    }
}