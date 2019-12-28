<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\EventListener;

use KaroIO\MessengerMonitorBundle\Stamp\MonitorIdStamp;
use KaroIO\MessengerMonitorBundle\Test\Message;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;

final class AddStampOnMessageSentListenerTest extends TestCase
{
    public function testAddStampOnMessageSent(): void
    {
        $listener = new AddStampOnMessageSentListener();
        $listener->onMessageSent($event = new SendMessageToTransportsEvent(new Envelope(new Message())));

        $this->assertNotNull($event->getEnvelope()->last(MonitorIdStamp::class));
    }
}
