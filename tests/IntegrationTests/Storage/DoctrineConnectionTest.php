<?php declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\IntegrationTests\Storage;

use Doctrine\DBAL\Connection;
use KaroIO\MessengerMonitorBundle\Storage\DoctrineConnection;
use KaroIO\MessengerMonitorBundle\Storage\StoredMessage;
use KaroIO\MessengerMonitorBundle\Test\Message;
use KaroIO\MessengerMonitorBundle\Test\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineConnectionTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function setUp(): void
    {
        self::bootKernel();

        /** @var Connection $connection */
        $connection = self::$container->get('doctrine.dbal.default_connection');

        try {
            $connection->connect();
        } catch (\Exception $exception) {
            $this->markTestSkipped(sprintf('Can\'t connect to connection: %s', $exception->getMessage()));
        }

        $connection->executeQuery('DROP TABLE IF EXISTS messenger_monitor');
    }

    public function testSaveAndLoadMessage(): void
    {
        /** @var DoctrineConnection $doctrineConnection */
        $doctrineConnection = self::$container->get('karo-io.messenger_monitor.storage.doctrine_connection');

        $doctrineConnection->saveMessage(
            new StoredMessage('id', Message::class, $dispatchedAt = new \DateTimeImmutable())
        );

        $storedMessage = $doctrineConnection->findMessage('id');

        $this->assertEquals(new StoredMessage('id', Message::class, $dispatchedAt), $storedMessage);
    }

    public function testSeveralMessages(): void
    {
        /** @var DoctrineConnection $doctrineConnection */
        $doctrineConnection = self::$container->get('karo-io.messenger_monitor.storage.doctrine_connection');

        $doctrineConnection->saveMessage(new StoredMessage('id1', Message::class, new \DateTimeImmutable()));
        $doctrineConnection->saveMessage(new StoredMessage('id2', Message::class, new \DateTimeImmutable()));

        $this->assertInstanceOf(StoredMessage::class, $doctrineConnection->findMessage('id1'));
        $this->assertInstanceOf(StoredMessage::class, $doctrineConnection->findMessage('id2'));
    }

    public function testUpdateMessage(): void
    {
        /** @var DoctrineConnection $doctrineConnection */
        $doctrineConnection = self::$container->get('karo-io.messenger_monitor.storage.doctrine_connection');

        $doctrineConnection->saveMessage($storedMessage = new StoredMessage('id', Message::class, new \DateTimeImmutable()));
        $storedMessage->setReceivedAt();
        $doctrineConnection->updateMessage($storedMessage);

        $storedMessageLoadedFromDatabase = $doctrineConnection->findMessage('id');

        $this->assertEquals(
            $storedMessage,
            $storedMessageLoadedFromDatabase
        );
    }
}
