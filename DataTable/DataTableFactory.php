<?php
namespace Sjdeboer\DataTableBundle\DataTable;

use Sjdeboer\DataTableBundle\Builder\TableBuilder;
use Sjdeboer\DataTableBundle\Exception\DataTableException;
use Sjdeboer\DataTableBundle\Source\SourceInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DataTableFactory
 * @package Sjdeboer\DataTable
 */
class DataTableFactory
{
    /** @var \Twig_Environment */
    public $twig;

    /** @var Router */
    public $router;

    /** @var array */
    public $config;

    /** @var array */
    private $optionsDefined = ['id', 'datatables_options', 'row_id', 'row_class', 'row_data', 'row_attr', 'table_class'];

    /** @var array */
    private $optionsRequired = ['data_source'];

    /** @var array */
    private $optionsAllowedTypes = [
        'id' => 'string',
        'data_source' => SourceInterface::class,
        'datatables_options' => 'array',
        'row_id' => 'callable',
        'row_class' => 'callable',
        'row_data' => 'callable',
        'row_attr' => 'callable',
        'table_class' => 'string',
    ];

    /** @var array */
    private $optionsAllowedValues = [];

    /** @var array */
    private $optionDefaults = [];

    /**
     * DataTableFactory constructor.
     * @param array $config
     * @param \Twig_Environment $twig
     * @param Router $router
     */
    public function __construct(array $config, \Twig_Environment $twig, Router $router)
    {
        $this->config = $config;
        $this->twig = $twig;
        $this->router = $router;

        $this->optionDefaults['id'] = 'auto_' . bin2hex(random_bytes(6));
    }

    /**
     * @return OptionsResolver
     */
    private function createOptionsResolver()
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired($this->optionsRequired);
        $resolver->setDefined($this->optionsDefined);
        $resolver->setDefaults($this->optionDefaults);
        foreach ($this->optionsAllowedTypes as $option => $values) {
            $resolver->setAllowedTypes($option, $values);
        }
        foreach ($this->optionsAllowedValues as $option => $values) {
            $resolver->setAllowedValues($option, $values);
        }
        $resolver->setNormalizer('id', function(Options $options, $value) {
            return preg_replace('/[^A-Za-z0-9\_]+/', '', (string)$value);
        });

        return $resolver;
    }

    /**
     * @param array $options
     * @return TableBuilder
     */
    public function createBuilder(array $options = [])
    {
        $resolver = $this->createOptionsResolver();

        $options = $resolver->resolve($options);

        return new TableBuilder($this, $options);
    }

    /**
     * @param string $class
     * @param array $options
     * @return DataTable
     * @throws DataTableException
     */
    public function create($class, array $options = [])
    {
        if (!class_exists($class)) {
            throw new DataTableException('DataTable class not found');
        }

        $table = new $class;

        $resolver = $this->createOptionsResolver();

        if (method_exists($table, 'configureOptions')) {
            $table->configureOptions($resolver);
        }

        $options = $resolver->resolve($options);

        $builder = new TableBuilder($this, $options);
        if (method_exists($table, 'buildTable')) {
            $table->buildTable($builder, $options);
        }

        return $builder->getTable();
    }
}
