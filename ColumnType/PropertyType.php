<?php
namespace Sjdeboer\DataTableBundle\ColumnType;

use Sjdeboer\DataTableBundle\DataTable\DataTableFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class PropertyType
 * @package Sjdeboer\DataTableBundle\ColumnType
 */
class PropertyType extends ColumnType implements ColumnTypeInterface
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
        $this->setDefaults($resolver, $options);

        $resolver->setRequired(['property']);
        $resolver->setAllowedTypes('property', 'string');

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
        $accessor = PropertyAccess::createPropertyAccessor();

        if (is_array($row) && !preg_match('/[\[\]]+/', $this->options['property'])) {
            $propertyPath = '[' . $this->options['property'] . ']';
        } else {
            $propertyPath = &$this->options['property'];
        }

        return $accessor->getValue($row, $propertyPath);
    }
}
