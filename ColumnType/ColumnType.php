<?php
namespace Sjdeboer\DataTableBundle\ColumnType;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ColumnType
 * @package Sjdeboer\DataTableBundle\ColumnType
 */
abstract class ColumnType
{
    /**
     * @param OptionsResolver $resolver
     * @param array $options
     */
    protected function setDefaults(OptionsResolver $resolver, array &$options) {
        $resolver->setDefined(['label', 'column_options']);
        $resolver->setAllowedTypes('label', 'string');
        $resolver->setAllowedTypes('column_options', 'array');

        $defaults = [
            'label' => '',
            'column_options' => [
                'orderable' => false,
            ],
        ];
        $resolver->setDefaults($defaults);

        if (array_key_exists('column_options', $options)) {
            $options['column_options'] = array_replace_recursive($defaults['column_options'], $options['column_options']);
        }
    }
}
