<?php declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\Storage;

use KaroIO\MessengerMonitorBundle\Stamp\MonitorIdStamp;
use KaroIO\MessengerMonitorBundle\Test\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;

final class StoredMessageTest extends TestCase
{
    public function testStoredMessage(): void
    {
        $storedMessage = new StoredMessage('id', Message::class, $dispatchedAt = new \DateTimeImmutable());

        $this->assertSame('id', $storedMessage->getId());
        $this->assertSame(Message::class, $storedMessage->getMessageClass());
        $this->assertSame($dispatchedAt->format('Y-m-d'), $storedMessage->getDispatchedAt()->format('Y-m-d'));
    }

    public function testCreateFromEnvelope(): void
    {
        $storedMessage = StoredMessage::fromEnvelope(
            new Envelope(new Message(), [$stamp = new MonitorIdStamp()])
        );

        $this->assertSame($stamp->getId(), $storedMessage->getId());
        $this->assertSame(Message::class, $storedMessage->getMessageClass());
    }

    public function testExceptionWhenCreateFromEnvelopeWithoutStamp(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Envelope should have a MonitorIdStamp!');

        StoredMessage::fromEnvelope(new Envelope(new Message()));
    }
}
