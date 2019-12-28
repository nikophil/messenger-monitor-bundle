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

    /** {@inheritdoc} */
    public function processStatistics(): array
    {
        // todo: this period should be chosen by user
        $this->periodTo = \DateTimeImmutable::createFromFormat('U', (string) time());
        //24 hours ago
        $this->periodFrom = \DateTimeImmutable::createFromFormat('U', (string) (time() - (60 * 60 * 24)));

        return $this->mergeMetrics();
    }

    private function mergeMetrics(): array
    {
        $metricsPerMessage = [];

        $this->extractMetric(
            $this->computeMessagesHandledPerHour(),
            'nb_messages_handled_per_hour',
            $metricsPerMessage
        );

        $this->extractMetric(
            $this->storedMessageRepository->getAverageWaitingTimeForPeriod($this->periodFrom, $this->periodTo),
            'average_waiting_time',
            $metricsPerMessage
        );

        $this->extractMetric(
            $this->storedMessageRepository->getAverageHandlingTimeForPeriod($this->periodFrom, $this->periodTo),
            'average_handling_time',
            $metricsPerMessage
        );

        return array_map(
            static function (string $class, array $metrics) {
                return Metrics::fromArray($class, $metrics);
            },
            array_keys($metricsPerMessage),
            $metricsPerMessage
        );
    }

    private function computeMessagesHandledPerHour(): array
    {
        $nbMessagesHandledOnPeriodGroupedByClass = $this->storedMessageRepository->getNbMessagesHandledForPeriod($this->periodFrom, $this->periodTo);
        $nbHoursInPeriod = abs($this->periodFrom->getTimestamp() - $this->periodTo->getTimestamp()) / (60 * 60);

        return array_map(
            static function ($nbMessagesHandledOnPeriod) use ($nbHoursInPeriod) {
                return round($nbMessagesHandledOnPeriod / $nbHoursInPeriod, 2);
            },
            $nbMessagesHandledOnPeriodGroupedByClass
        );
    }

    private function extractMetric(array $newMetric, string $fieldName, array &$metricsPerMessage): void
    {
        foreach ($newMetric as $message => $nbMessagesHandledPerHour) {
            if (!isset($metricsPerMessage[$message])) {
                $metricsPerMessage[$message] = [];
            }

            $metricsPerMessage[$message][$fieldName] = $nbMessagesHandledPerHour;
        }
    }
}
