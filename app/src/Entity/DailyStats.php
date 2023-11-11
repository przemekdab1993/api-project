<?php


namespace App\Entity;


use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'get',
    ],
    itemOperations: [
        'get'
    ],
    shortName: 'daily-stats',
    normalizationContext: [
        'groups' => [
            'daily-stats:read'
        ]
    ],
    paginationItemsPerPage: 5
)]
class DailyStats
{
    #[Groups(['daily-stats:read'])]
    public \DateTimeImmutable $date;

    #[Groups(['daily-stats:read'])]
    public int $totalVisitors;

    /**
     * @var array<CheeseListing> | CheeseListing[]
     */
    #[Groups(['daily-stats:read'])]
    public $mostPopularListings = [];

    /**
     * @param CheeseListing[] $mostPopularListings
     */
    public function __construct(\DateTimeImmutable $date, int $totalVisitors, array $mostPopularListings)
    {
        $this->date = $date;
        $this->totalVisitors = $totalVisitors;
        $this->mostPopularListings = $mostPopularListings;
    }


    #[ApiProperty(identifier: true)]
    public function getDateString(): string
    {
        return $this->date->format('Y-m-d');
    }
}