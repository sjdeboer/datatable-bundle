<?php

namespace Sjdeboer\DataTableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Sjdeboer\DataTableBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sdeboer_data_table');

        $rootNode
            ->children()
                ->scalarNode('default_table_class')
                    ->info('Default class added to the <table> element')
                    ->defaultValue('')
                ->end()
                ->node('default_datatables_options', 'variable')
                    ->info('Default Datatables options. For available options, see: https://datatables.net/reference/option/')
                    ->defaultValue([
                        'searching' => false,
                    ])
                ->end()
            ->end();

        return $treeBuilder;
    }
}
