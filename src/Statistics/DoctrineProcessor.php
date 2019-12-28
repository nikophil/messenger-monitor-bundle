<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\Statistics;

use KaroIO\MessengerMonitorBundle\Storage\StoredMessageRepository;

/**
 * @internal
 */
final class DoctrineProcessor implements StatisticsProcessor
{
    private $storedMessageRepository;

    /** @var \DateTimeImmutable */
    private $periodFrom;
    /** @var \DateTimeImmutable */
    private $periodTo;

    public function __construct(StoredMessageRepository $storedMessageRepository)
    {
        $this->storedMessageRepository = $storedMessageRepository;
    }

    public function processStatistics(): Metrics
    {
        // todo: this period should be chosen by user
        $this->periodFrom = \DateTimeImmutable::createFromFormat('U', (string) (time() - (60 * 60 * 24)));
        $this->periodTo = \DateTimeImmutable::createFromFormat('U', (string) time());

        return new Metrics(
            $this->computeMessagesHandledPerHour(),
            $this->storedMessageRepository->getAverageWaitingTimeForPeriod($this->periodFrom, $this->periodTo),
            $this->storedMessageRepository->getAverageHandlingTimeForPeriod($this->periodFrom, $this->periodTo)
        );
    }

    private function computeMessagesHandledPerHour(): int
    {
        $nbMessagesHandledOnPeriod = $this->storedMessageRepository->getNbMessagesHandledForPeriod($this->periodFrom, $this->periodTo);

        $nbHoursInPeriod = abs($this->periodFrom->getTimestamp() - $this->periodTo->getTimestamp()) / (60 * 60);

        return (int) ($nbMessagesHandledOnPeriod / $nbHoursInPeriod);
    }
}
