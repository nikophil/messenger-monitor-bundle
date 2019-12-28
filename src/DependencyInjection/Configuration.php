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
        $treeBuilder = new TreeBuilder('messenger_monitor');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->enumNode('driver')
                ->defaultValue('doctrine')
                ->values(['doctrine', 'redis'])
            ->end();

        return $treeBuilder;
    }
}
