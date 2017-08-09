<?php

namespace Sjdeboer\DataTableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
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
                    ->info('Default class added to the HTML table element')
                    ->defaultValue('')
                ->end()
                ->node('default_datatables_options', 'variable')
                    ->info('For available options, see: https://datatables.net/reference/option/')
                    ->defaultValue([
                        'searching' => false,
                        'searchDelay' => 400,
                        'stateSave' => false,
                        'lengthChange' => true,
                        'pageLength' => 10,
                        'lengthMenu' => [ 10, 25, 50, 75, 100 ],
                        'language' => [
                            'sEmptyTable' => 'No data available in table',
                            'sInfo' => 'Showing _START_ to _END_ of _TOTAL_ entries',
                            'sInfoEmpty' => 'Showing 0 to 0 of 0 entries',
                            'sInfoFiltered' => '(filtered from _MAX_ total entries)',
                            'sInfoPostFix' => '',
                            'sInfoThousands' => ',',
                            'sLengthMenu' => 'Show _MENU_ entries',
                            'sLoadingRecords' => 'Loading...',
                            'sProcessing' => 'Processing...',
                            'sSearch' => 'Search',
                            'sZeroRecords' => 'No matching records found',
                            'oPaginate' => [
                                'sFirst' => 'First',
                                'sLast' => 'Last',
                                'sNext' => 'Next',
                                'sPrevious' => 'Previous',
                            ],
                            'oAria' => [
                                'sSortAscending' => ' activate to sort column ascending',
                                'sSortDescending' => ' activate to sort column descending'
                            ],
                        ],
                    ])
                ->end()
            ->end();

        return $treeBuilder;
    }
}
