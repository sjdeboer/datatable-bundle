<?php
namespace Sjdeboer\DataTableBundle\Builder;

use Sjdeboer\DataTableBundle\ColumnType\ColumnTypeInterface;
use Sjdeboer\DataTableBundle\DataTable\DataTable;
use Sjdeboer\DataTableBundle\DataTable\DataTableFactory;
use Sjdeboer\DataTableBundle\Exception\DataTableException;

/**
 * Class TableBuilder
 * @package Sjdeboer\DataTable
 */
class TableBuilder implements TableBuilderInterface
{
    /** @var DataTableFactory */
    private $factory;

    /** @var array */
    private $options = [];

    /** @var ColumnTypeInterface[] */
    private $columns = [];

    /**
     * TableBuilder constructor.
     * @param DataTableFactory $factory
     * @param array $options
     */
    public function __construct(DataTableFactory $factory, array $options = [])
    {
        $this->factory = $factory;
        $this->options = &$options;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->columns);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->columns);
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->columns);
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->current() !== false;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->columns);
    }

    /**
     * @inheritdoc
     */
    public function add($type = null, array $options = array())
    {
        if (!is_string($type)) {
            throw new DataTableException('Column type should be a string classname');
        }

        $column = new $type();

        if (!($column instanceof ColumnTypeInterface)) {
            throw new DataTableException('Column object should implement ColumnTypeInterface');
        }

        $column->setFactory($this->factory)->setOptions($options);

        $this->columns[] = $column;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTable()
    {
        return new DataTable($this->factory, $this, $this->options);
    }
}
