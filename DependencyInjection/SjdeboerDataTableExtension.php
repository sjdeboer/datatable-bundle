<?php

namespace Sjdeboer\DataTableBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class SjdeboerDataTableExtension
 * @package Sjdeboer\DataTableBundle\DependencyInjection
 */
class SjdeboerDataTableExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('sdeboer_data_table.default_table_class', $config['default_table_class']);
        $container->setParameter('sdeboer_data_table.default_datatables_options', $config['default_datatables_options']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
