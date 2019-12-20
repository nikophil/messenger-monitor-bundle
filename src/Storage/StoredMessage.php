<?php declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\Storage;

use KaroIO\MessengerMonitorBundle\Stamp\MonitorIdStamp;
use Symfony\Component\Messenger\Envelope;

// todo handle retries
final class StoredMessage
{
    private $id;
    private $messageClass;
    private $dispatchedAt;
    private $receivedAt;

    public function __construct(string $id, string $messageClass, \DateTimeImmutable $dispatchedAt, ?\DateTimeImmutable $receivedAt = null)
    {
        $this->id = $id;
        $this->messageClass = $messageClass;
        $this->dispatchedAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dispatchedAt->format('Y-m-d H:i:s'));

        if (null !== $receivedAt) {
            $this->receivedAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $receivedAt->format('Y-m-d H:i:s'));
        }
    }

    public static function fromEnvelope(Envelope $envelope): self
    {
        /** @var MonitorIdStamp $monitorIdStamp */
        $monitorIdStamp = $envelope->last(MonitorIdStamp::class);

        if (null === $monitorIdStamp) {
            throw new \RuntimeException('Envelope should have a MonitorIdStamp!');
        }

        return new self(
            $monitorIdStamp->getId(),
            get_class($envelope->getMessage()),
            \DateTimeImmutable::createFromFormat('U', (string) time())
        );
    }

    public static function fromDatabaseRow(array $row): self
    {
        return new self(
            $row['id'],
            $row['class'],
            new \DateTimeImmutable($row['dispatched_at']),
            $row['received_at'] !== null ? new \DateTimeImmutable($row['received_at']) : null
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMessageClass(): string
    {
        return $this->messageClass;
    }

    public function getDispatchedAt(): \DateTimeImmutable
    {
        return $this->dispatchedAt;
    }

    public function setReceivedAt(): void
    {
        $this->receivedAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
    }

    public function getReceivedAt(): ?\DateTimeImmutable
    {
        return $this->receivedAt;
    }
}
