<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\Statistics;

/**
 * @internal
 */
interface StatisticsProcessor
{
    /** @return Metrics[] */
    public function processStatistics(): array;
}
