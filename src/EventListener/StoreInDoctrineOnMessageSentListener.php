<?php declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\EventListener;

use KaroIO\MessengerMonitorBundle\Storage\DoctrineConnection;
use KaroIO\MessengerMonitorBundle\Storage\StoredMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;

// todo: this should be conditionally declared as a service depending on storage driver
final class StoreInDoctrineOnMessageSentListener implements EventSubscriberInterface
{
    private $doctrineConnection;

    public function __construct(DoctrineConnection $doctrineConnection)
    {
        $this->doctrineConnection = $doctrineConnection;
    }

    public function onMessageSent(SendMessageToTransportsEvent $event): void
    {
        $this->doctrineConnection->saveMessage(StoredMessage::fromEnvelope($event->getEnvelope()));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // should happen after AddStampOnMessageSentListener
            SendMessageToTransportsEvent::class => ['onMessageSent', 1],
        ];
    }
}
