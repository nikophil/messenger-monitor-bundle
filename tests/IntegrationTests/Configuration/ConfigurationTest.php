<?php declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\IntegrationTests\Configuration;

use KaroIO\MessengerMonitorBundle\Test\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final class ConfigurationTest extends TestCase
{
    public function testUseTableNameWithRedisDriverThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('"table_name" can only be used with doctrine driver.');

        $kernel = new TestKernel(
            [
                'driver' => 'redis',
                'table_name' => 'foo'
            ]
        );
        $kernel->boot();
    }
}
