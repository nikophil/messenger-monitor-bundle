<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @internal
 */
final class KaroIOMessengerMonitorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        if ('doctrine' !== $config['driver']) {
            // todo: remove definition from all services tagged with "messenger_monitor.doctrine_driver"?
            $container->removeDefinition('karo-io.messenger_monitor.storage.doctrine_connection');
        } else {
            $doctrineConnectionFactoryDefinition = $container->getDefinition('karo-io.messenger_monitor.storage.doctrine_connection_factory');

            $doctrineConnection = $config['doctrine_connection'] ?? 'default';
            $doctrineConnectionFactoryDefinition->replaceArgument(1, $doctrineConnection);

            $tableName = $config['table_name'] ?? 'karo_io_messenger_monitor';
            $doctrineConnectionFactoryDefinition->replaceArgument(2, $tableName);
        }
    }
}
