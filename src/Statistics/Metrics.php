<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\Statistics;

/**
 * @internal
 */
final class Metrics
{
    private $messagesHandledPerHour;
    private $averageWaitingTime;
    private $averageHandlingTime;

    public function __construct(int $messagesHandledPerHour, int $averageWaitingTime, int $averageHandlingTime)
    {
        $this->messagesHandledPerHour = $messagesHandledPerHour;
        $this->averageWaitingTime = $averageWaitingTime;
        $this->averageHandlingTime = $averageHandlingTime;
    }

    public function getMessagesHandledPerHour(): int
    {
        return $this->messagesHandledPerHour;
    }

    public function getAverageWaitingTime(): int
    {
        return $this->averageWaitingTime;
    }

    public function getAverageHandlingTime(): int
    {
        return $this->averageHandlingTime;
    }
}
