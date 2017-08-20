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
    use ColumnTypeDefault;

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
        $this->setDefaults($resolver, $options);

        $resolver->setRequired(['closure']);
        $resolver->setAllowedTypes('closure', 'callable');

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
