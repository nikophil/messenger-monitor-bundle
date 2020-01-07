<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\Statistics;

/**
 * @internal
 */
final class RedisProcessor implements StatisticsProcessor
{
    /** {@inheritdoc} */
    public function processStatistics(): array
    {
        return [];
    }
}
