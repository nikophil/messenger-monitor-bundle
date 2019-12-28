<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\IntegrationTests\Storage;

use KaroIO\MessengerMonitorBundle\IntegrationTests\AbstractDoctrineIntegrationTests;
use KaroIO\MessengerMonitorBundle\Storage\DoctrineConnection;

final class DoctrineConnectionTest extends AbstractDoctrineIntegrationTests
{
    public function testExecuteQueryUpdatesSchema(): void
    {
        /** @var DoctrineConnection $doctrineConnection */
        $doctrineConnection = self::$container->get('karo-io.messenger_monitor.storage.doctrine_connection');

        $statement = $doctrineConnection->executeQuery(
            <<<SQL
SHOW COLUMNS FROM messenger_monitor
SQL
        );

        $this->assertCount(6, $statement->fetchAll());
    }
}
