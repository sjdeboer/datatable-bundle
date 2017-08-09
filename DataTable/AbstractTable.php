<?php
namespace Sjdeboer\DataTableBundle\DataTable;

use Sjdeboer\DataTableBundle\Builder\TableBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractTable
 * @package Sjdeboer\DataTable
 */
abstract class AbstractTable
{
    public function buildTable(TableBuilderInterface $builder, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
