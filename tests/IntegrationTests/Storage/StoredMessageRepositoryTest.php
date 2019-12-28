<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\IntegrationTests\Storage;

use KaroIO\MessengerMonitorBundle\IntegrationTests\AbstractDoctrineIntegrationTests;
use KaroIO\MessengerMonitorBundle\Storage\StoredMessage;
use KaroIO\MessengerMonitorBundle\Storage\StoredMessageRepository;
use KaroIO\MessengerMonitorBundle\Test\Message;

final class StoredMessageRepositoryTest extends AbstractDoctrineIntegrationTests
{
    public function testSaveAndLoadMessage(): void
    {
        /** @var StoredMessageRepository $storedMessageRepository */
        $storedMessageRepository = self::$container->get('karo-io.messenger_monitor.storage.stored_message_repository');

        $storedMessageRepository->saveMessage(
            new StoredMessage('id', Message::class, $dispatchedAt = (new \DateTimeImmutable())->setTime(0, 0, 0))
        );

        $storedMessage = $storedMessageRepository->findMessage('id');

        $this->assertEquals(new StoredMessage('id', Message::class, $dispatchedAt), $storedMessage);
    }

    public function testSeveralMessages(): void
    {
        /** @var StoredMessageRepository $storedMessageRepository */
        $storedMessageRepository = self::$container->get('karo-io.messenger_monitor.storage.stored_message_repository');

        $storedMessageRepository->saveMessage(new StoredMessage('id1', Message::class, new \DateTimeImmutable()));
        $storedMessageRepository->saveMessage(new StoredMessage('id2', Message::class, new \DateTimeImmutable()));

        $this->assertInstanceOf(StoredMessage::class, $storedMessageRepository->findMessage('id1'));
        $this->assertInstanceOf(StoredMessage::class, $storedMessageRepository->findMessage('id2'));
    }

    public function testUpdateMessage(): void
    {
        /** @var StoredMessageRepository $storedMessageRepository */
        $storedMessageRepository = self::$container->get('karo-io.messenger_monitor.storage.stored_message_repository');

        $storedMessageRepository->saveMessage($storedMessage = new StoredMessage('id', Message::class, new \DateTimeImmutable()));
        $storedMessage->setReceivedAt(\DateTimeImmutable::createFromFormat('U', (string) time()));
        $storedMessage->setHandledAt(\DateTimeImmutable::createFromFormat('U', (string) time()));
        $storedMessageRepository->updateMessage($storedMessage);

        $storedMessageLoadedFromDatabase = $storedMessageRepository->findMessage('id');

        $this->assertSame(
            $storedMessage->getReceivedAt()->format('Y-m-d H:i:s'),
            $storedMessageLoadedFromDatabase->getReceivedAt()->format('Y-m-d H:i:s')
        );

        $this->assertSame(
            $storedMessage->getReceivedAt()->format('Y-m-d H:i:s'),
            $storedMessageLoadedFromDatabase->getReceivedAt()->format('Y-m-d H:i:s')
        );
    }
}
