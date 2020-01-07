<?php

declare(strict_types=1);

namespace KaroIO\MessengerMonitorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @internal
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('karo_io_messenger_monitor');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->enumNode('driver')
                    ->defaultValue('doctrine')
                    ->values(['doctrine', 'redis'])
                ->end()
                ->scalarNode('table_name')
                    ->defaultNull()
                ->end()
            ->end()
            ->validate()
            ->ifTrue(function($value) {
                return null !== $value['table_name'] && $value['driver'] === 'redis';
            })
            ->thenInvalid('"table_name" can only be used with doctrine driver.');

        return $treeBuilder;
    }
}
