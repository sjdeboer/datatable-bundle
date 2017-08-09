<?php
namespace Sjdeboer\DataTableBundle\ColumnType;

use Sjdeboer\DataTableBundle\DataTable\DataTableFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ClosureType
 * @package Sjdeboer\DataTableBundle\ColumnType
 */
class ClosureType implements ColumnTypeInterface
{
    /** @var DataTableFactory */
    private $factory;

    /** @var array */
    private $options;

    /**
     * @inheritdoc
     */
    public function setFactory(DataTableFactory $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setOptions(array $options = [])
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(['closure']);
        $resolver->setDefined(['label', 'column_options']);

        $resolver->setAllowedTypes('closure', 'callable');
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
            $options['column_options'] = array_merge($defaults['column_options'], $options['column_options']);
        }

        $this->options = $resolver->resolve($options);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @inheritdoc
     */
    public function createHeadView()
    {
        return $this->options['label'];
    }

    /**
     * @inheritdoc
     */
    public function createRowView($row)
    {
        return $this->options['closure']($row, $this->factory->router);
    }
}
