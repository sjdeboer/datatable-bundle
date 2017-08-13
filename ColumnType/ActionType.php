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
        $groupResolver->setRequired(['actions']);
        $groupResolver->setAllowedTypes('attr', 'array');
        $groupResolver->setAllowedTypes('actions', 'array');
        $groupResolver->setDefaults([
            'attr' => [],
            'actions' => []
        ]);

        $actionResolver = new OptionsResolver();
        $actionResolver->setDefined(['attr', 'icon']);
        $actionResolver->setRequired(['label']);
        $actionResolver->setDefaults([
            'attr' => [],
            'icon' => null,
            'label' => '',
        ]);

        foreach ($output as $group) {
            if (!is_array($group)) {
                throw new DataTableException('Action group should be an array');
            }
            $groupResolver->resolve($group);

            foreach ($group['actions'] as $action) {
                if (!is_array($action)) {
                    throw new DataTableException('Action should be an array');
                }
                $actionResolver->resolve($action);
            }
        }

        return $this->factory->twig->render('@SjdeboerDataTable/action.html.twig', [
            'groups' => $output,
        ]);
    }
}
