<?php declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\EventListener;

use KaroIO\MessengerMonitorBundle\Stamp\MonitorIdStamp;
use KaroIO\MessengerMonitorBundle\Storage\DoctrineConnection;
use KaroIO\MessengerMonitorBundle\Storage\StoredMessage;
use KaroIO\MessengerMonitorBundle\Test\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;

final class UpdateInDoctrineListenerTest extends TestCase
{
    public function setUp(): void
    {

    }

    public function testUpdateInDoctrineOnMessageReceived(): void
    {
        $listener = new UpdateInDoctrineListener(
            $doctrineConnection = $this->createMock(DoctrineConnection::class)
        );

        $envelope = new Envelope(new Message(), [$stamp = new MonitorIdStamp()]);

        $doctrineConnection->expects($this->once())
            ->method('findMessage')
            ->with($stamp->getId())
            ->willReturn($storedMessage = new StoredMessage($stamp->getId(), Message::class, new \DateTimeImmutable()));

        $doctrineConnection->expects($this->once())
            ->method('updateMessage')
            ->with($storedMessage);

        $listener->onMessageReceived(new SendMessageToTransportsEvent($envelope));
        $this->assertNotNull($storedMessage->getReceivedAt());
    }

    public function testUpdateInDoctrineOnMessageHandled(): void
    {
        $listener = new UpdateInDoctrineListener(
            $doctrineConnection = $this->createMock(DoctrineConnection::class)
        );

        $envelope = new Envelope(new Message(), [$stamp = new MonitorIdStamp()]);

        $doctrineConnection->expects($this->once())
            ->method('findMessage')
            ->with($stamp->getId())
            ->willReturn($storedMessage = new StoredMessage($stamp->getId(), Message::class, new \DateTimeImmutable(), new \DateTimeImmutable()));

        $doctrineConnection->expects($this->once())
            ->method('updateMessage')
            ->with($storedMessage);

        $listener->onMessageHandled(new SendMessageToTransportsEvent($envelope));
        $this->assertNotNull($storedMessage->getHandledAt());
    }
}
