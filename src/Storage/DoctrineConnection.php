<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\Storage;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Synchronizer\SingleDatabaseSynchronizer;
use Doctrine\DBAL\Types\Types;

/**
 * todo: use some config to select connection and table name
 *
 * @internal
 */
class DoctrineConnection
{
    private $driverConnection;
    private $schemaSynchronizer;
    private $tableName;

    public function __construct(DBALConnection $driverConnection, string $tableName = 'messenger_monitor')
    {
        $this->driverConnection = $driverConnection;
        $this->schemaSynchronizer = new SingleDatabaseSynchronizer($this->driverConnection);
        $this->tableName = $tableName;
    }

    public function executeQuery(string $sql, array $parameters = [], array $types = []): ResultStatement
    {
        try {
            $stmt = $this->driverConnection->executeQuery($sql, $parameters, $types);
        } catch (TableNotFoundException $e) {
            if ($this->driverConnection->isTransactionActive()) {
                throw $e;
            }

            $this->setup();

            $stmt = $this->driverConnection->executeQuery($sql, $parameters, $types);
        }

        return $stmt;
    }

    private function setup(): void
    {
        $this->schemaSynchronizer->updateSchema($this->getSchema(), true);
    }

    private function getSchema(): Schema
    {
        $schema = new Schema([], [], $this->driverConnection->getSchemaManager()->createSchemaConfig());
        $table = $schema->createTable($this->tableName);
        $table->addColumn('id', Types::GUID)->setNotnull(true);
        $table->addColumn('class', Types::STRING)->setLength(255)->setNotnull(true);
        $table->addColumn('dispatched_at', Types::DATETIME_IMMUTABLE)->setNotnull(true);
        $table->addColumn('received_at', Types::DATETIME_IMMUTABLE)->setNotnull(false);
        $table->addColumn('handled_at', Types::DATETIME_IMMUTABLE)->setNotnull(false);
        $table->addColumn('retries', Types::INTEGER)->setDefault(0);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['dispatched_at']);
        $table->addIndex(['class']);

        return $schema;
    }
}
