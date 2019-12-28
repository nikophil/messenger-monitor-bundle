<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\Storage;

/**
 * @internal
 */
class StoredMessageRepository
{
    private $doctrineConnection;
    private $tableName;

    public function __construct(DoctrineConnection $doctrineConnection, string $tableName = 'messenger_monitor')
    {
        $this->doctrineConnection = $doctrineConnection;
        $this->tableName = $tableName;
    }

    public function saveMessage(StoredMessage $storedMessage): void
    {
        $this->doctrineConnection->executeQuery(
            <<<SQL
INSERT INTO {$this->tableName}
(id, class, dispatched_at)
VALUES (:id, :class, :dispatched_at)
SQL
            ,
            [
                'id' => $storedMessage->getId(),
                'class' => $storedMessage->getMessageClass(),
                'dispatched_at' => $storedMessage->getDispatchedAt()->format('Y-m-d H:i:s'),
            ]
        );
    }

    public function updateMessage(StoredMessage $storedMessage): void
    {
        $this->doctrineConnection->executeQuery(
            <<<SQL
UPDATE {$this->tableName}
    SET received_at = :received_at
WHERE id = :id
SQL
            ,
            [
                'received_at' => null !== $storedMessage->getReceivedAt() ? $storedMessage->getReceivedAt()->format('Y-m-d H:i:s') : null,
                'handled_at' => null !== $storedMessage->getHandledAt() ? $storedMessage->getHandledAt()->format('Y-m-d H:i:s') : null,
                'id' => $storedMessage->getId(),
            ]
        );
    }

    public function findMessage(string $id): ?StoredMessage
    {
        $statement = $this->doctrineConnection->executeQuery(
            <<<SQL
SELECT * FROM {$this->tableName} WHERE id = :id
SQL
            ,
            ['id' => $id]
        );

        if (false === $row = $statement->fetch()) {
            return null;
        }

        return StoredMessage::fromDatabaseRow($row);
    }

    public function getNbMessagesHandledForPeriod(\DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        return (int) $this->doctrineConnection->executeQuery(
            <<<SQL
SELECT count(id) FROM {$this->tableName}
WHERE handled_at >= :from
AND handled_at <= :to
SQL
            ,
            ['from' => $from->format('Y:m:d H:i:s'), 'to' => $to->format('Y:m:d H:i:s')]
        )->fetchColumn();
    }

    public function getAverageWaitingTimeForPeriod(\DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        return (int) $this->doctrineConnection->executeQuery(
            <<<SQL
SELECT AVG(TIME_TO_SEC(TIMEDIFF(received_at, dispatched_at))) FROM {$this->tableName}
WHERE received_at IS NOT NULL
AND received_at >= :from
AND received_at <= :to
SQL
            ,
            ['from' => $from->format('Y:m:d H:i:s'), 'to' => $to->format('Y:m:d H:i:s')]
        )->fetchColumn();
    }

    public function getAverageHandlingTimeForPeriod(\DateTimeImmutable $from, \DateTimeImmutable $to): int
    {
        return (int) $this->doctrineConnection->executeQuery(
            <<<SQL
SELECT AVG(TIME_TO_SEC(TIMEDIFF(handled_at, received_at))) FROM {$this->tableName}
WHERE handled_at IS NOT NULL
AND handled_at >= :from
AND handled_at <= :to
SQL
            ,
            ['from' => $from->format('Y:m:d H:i:s'), 'to' => $to->format('Y:m:d H:i:s')]
        )->fetchColumn();
    }
}
