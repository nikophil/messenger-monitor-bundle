<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * @internal
 */
final class MonitorIdStamp implements StampInterface
{
    private $id;

    public function __construct()
    {
        $this->id = uuid_create(UUID_TYPE_RANDOM);
    }

    public function getId(): string
    {
        return $this->id;
    }
}
