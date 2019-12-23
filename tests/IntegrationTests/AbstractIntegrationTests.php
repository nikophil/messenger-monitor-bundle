<?php declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\IntegrationTests;

use KaroIO\MessengerMonitorBundle\Test\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractIntegrationTests extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function setUp(): void
    {
        self::bootKernel();
    }
}
