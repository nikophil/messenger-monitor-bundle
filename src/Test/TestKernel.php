<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\Test;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use KaroIO\MessengerMonitorBundle\KaroIOMessengerMonitorBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new KaroIOMessengerMonitorBundle(),
            new TwigBundle(),
        ];
    }

    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->setParameter('kernel.secret', 123);
        $c->prependExtensionConfig('framework', ['session' => ['enabled' => true]]);
        $c->prependExtensionConfig(
            'doctrine',
            [
                'dbal' => [
                    'connections' => [
                        'default' => [
                            'url' => $_ENV['TEST_DATABASE_DSN'],
                            'logging' => false,
                        ],
                    ],
                ],
            ]
        );
    }

    public function getProjectDir()
    {
        return $this->getRootDir();
    }

    public function getRootDir()
    {
        return __DIR__.'/../../tests/tmp';
    }

    protected function build(ContainerBuilder $container)
    {
        // set all services public in order to access them
        // with static::$container->get('service') in tests
        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }
    }
}
