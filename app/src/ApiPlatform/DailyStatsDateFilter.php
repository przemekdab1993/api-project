<?php


namespace App\ApiPlatform;


use ApiPlatform\Core\Serializer\Filter\FilterInterface;
use Symfony\Component\HttpFoundation\Request;

class DailyStatsDateFilter implements FilterInterface
{
    public const FROM_FILTER_CONTEXT = 'daily_stats_from';

    public function apply(Request $request, bool $normalization, array $attributes, array &$context)
    {
        $from = $request->query->get('from');

        if (!$from) {
            return;
        }

        $fromDate = \DateTimeImmutable::createFromFormat('Y-m-d', $from);

        if ($fromDate) {
            $fromDate = $fromDate->setTime(0,0,0);

            $context[self::FROM_FILTER_CONTEXT] = $fromDate;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'from' => [
                'property' => null,
                'type' => 'string',
                'required' => false,
                'description' => 'From date e.g. 2020-09-01'
            ],
        ];
    }
}