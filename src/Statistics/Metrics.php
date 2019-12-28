<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\Statistics;

/**
 * @internal
 */
final class Metrics
{
    private $class;
    private $messagesHandledPerHour;
    private $averageWaitingTime;
    private $averageHandlingTime;

    public function __construct(string $class, ?float $messagesHandledPerHour, ?float $averageWaitingTime, ?float $averageHandlingTime)
    {
        $this->class = $class;
        $this->messagesHandledPerHour = $messagesHandledPerHour;
        $this->averageWaitingTime = $averageWaitingTime;
        $this->averageHandlingTime = $averageHandlingTime;
    }

    public static function fromArray(string $class, array $sourceArray): self
    {
        return new self(
            $class,
            $sourceArray['nb_messages_handled_per_hour'] ?? null,
            isset($sourceArray['average_waiting_time']) ? (float) $sourceArray['average_waiting_time'] : null,
            isset($sourceArray['average_handling_time']) ? (float) $sourceArray['average_handling_time'] : null
        );
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMessagesHandledPerHour(): ?int
    {
        return $this->messagesHandledPerHour;
    }

    public function getAverageWaitingTime(): ?float
    {
        return $this->averageWaitingTime;
    }

    public function getAverageHandlingTime(): ?float
    {
        return $this->averageHandlingTime;
    }
}
