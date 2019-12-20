<?php declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\EventListener;

use KaroIO\MessengerMonitorBundle\Stamp\MonitorIdStamp;
use KaroIO\MessengerMonitorBundle\Storage\DoctrineConnection;
use KaroIO\MessengerMonitorBundle\Storage\StoredMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;

// todo: this should be conditionally declared as a service depending on storage driver
final class UpdateInDoctrineOnMessageReceivedListener implements EventSubscriberInterface
{
    private $doctrineConnection;

    public function __construct(DoctrineConnection $doctrineConnection)
    {
        $this->doctrineConnection = $doctrineConnection;
    }

    public function onMessageReceived(SendMessageToTransportsEvent $event): void
    {
        /** @var MonitorIdStamp $monitorIdStamp */
        $monitorIdStamp = $event->getEnvelope()->last(MonitorIdStamp::class);

        if (null === $monitorIdStamp) {
            throw new \RuntimeException('Envelope should have a MonitorIdStamp!');
        }

        $storedMessage = $this->doctrineConnection->findMessage($monitorIdStamp->getId());

        if (null === $storedMessage) {
            throw new \RuntimeException(sprintf('Message with id "%s" not found', $monitorIdStamp->getId()));
        }

        $storedMessage->setReceivedAt();
        $this->doctrineConnection->updateMessage($storedMessage);

    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageReceivedEvent::class => ['onMessageReceived', 1],
        ];
    }
}
