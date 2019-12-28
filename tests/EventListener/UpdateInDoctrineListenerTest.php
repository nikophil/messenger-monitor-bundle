<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\EventListener;

use KaroIO\MessengerMonitorBundle\Stamp\MonitorIdStamp;
use KaroIO\MessengerMonitorBundle\Storage\StoredMessage;
use KaroIO\MessengerMonitorBundle\Storage\StoredMessageRepository;
use KaroIO\MessengerMonitorBundle\Test\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

final class UpdateInDoctrineListenerTest extends TestCase
{
    public function testUpdateInDoctrineOnMessageReceived(): void
    {
        $listener = new UpdateInDoctrineListener(
            $storedMessageRepository = $this->createMock(StoredMessageRepository::class)
        );

        $envelope = new Envelope(new Message(), [$stamp = new MonitorIdStamp()]);

        $storedMessageRepository->expects($this->once())
            ->method('findMessage')
            ->with($stamp->getId())
            ->willReturn($storedMessage = new StoredMessage($stamp->getId(), Message::class, new \DateTimeImmutable()));

        $storedMessageRepository->expects($this->once())
            ->method('updateMessage')
            ->with($storedMessage);

        $listener->onMessageReceived(new WorkerMessageReceivedEvent($envelope, 'receiver-name'));
        $this->assertNotNull($storedMessage->getReceivedAt());
    }

    public function testUpdateInDoctrineOnMessageHandled(): void
    {
        $listener = new UpdateInDoctrineListener(
            $storedMessageRepository = $this->createMock(StoredMessageRepository::class)
        );

        $envelope = new Envelope(new Message(), [$stamp = new MonitorIdStamp()]);

        $storedMessageRepository->expects($this->once())
            ->method('findMessage')
            ->with($stamp->getId())
            ->willReturn($storedMessage = new StoredMessage($stamp->getId(), Message::class, new \DateTimeImmutable(), new \DateTimeImmutable()));

        $storedMessageRepository->expects($this->once())
            ->method('updateMessage')
            ->with($storedMessage);

        $listener->onMessageHandled(new WorkerMessageHandledEvent($envelope, 'receiver-name'));
        $this->assertNotNull($storedMessage->getHandledAt());
    }
}
