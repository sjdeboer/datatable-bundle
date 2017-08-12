<?php
namespace Sjdeboer\DataTableBundle\ColumnType;

use Sjdeboer\DataTableBundle\DataTable\DataTableFactory;
use Sjdeboer\DataTableBundle\Exception\DataTableException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ActionType
 * @package Sjdeboer\DataTableBundle\ColumnType
 */
class ActionType extends ColumnType implements ColumnTypeInterface
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
        $output = $this->options['closure']($row, $this->factory->router);

        if (!is_array($output)) {
            throw new DataTableException('return value of ActionType closure should be an array');
        }

        $groupResolver = new OptionsResolver();
        $groupResolver->setDefined(['attr']);
        $groupResolver->setRequired(['options']);
        $groupResolver->setAllowedTypes('attr', 'array');
        $groupResolver->setAllowedTypes('options', 'array');
        $groupResolver->setDefaults([
            'attr' => [],
            'options' => []
        ]);

        $optionResolver = new OptionsResolver();
        $optionResolver->setDefined(['attr', 'icon']);
        $optionResolver->setRequired(['label']);
        $groupResolver->setDefaults([
            'attr' => [],
            'icon' => null,
            'label' => '',
        ]);

        foreach ($output as $group) {
            if (!is_array($group)) {
                throw new DataTableException('Action group should be an array');
            }
            $groupResolver->resolve($group);

            foreach ($group['options'] as $option) {
                if (!is_array($option)) {
                    throw new DataTableException('Action should be an array');
                }
                $optionResolver->resolve($option);
            }
        }

        return $this->factory->twig->render('@SjdeboerDataTable/action.html.twig', [
            'groups' => $output,
        ]);
    }
}
