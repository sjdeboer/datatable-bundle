<?php
namespace Sjdeboer\DataTableBundle\ColumnType;

use Sjdeboer\DataTableBundle\DataTable\DataTableFactory;

/**
 * Interface ColumnTypeInterface
 * @package Sjdeboer\DataTableBundle\ColumnType
 */
interface ColumnTypeInterface
{
    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options = []);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param DataTableFactory $factory
     * @return $this
     */
    public function setFactory(DataTableFactory $factory);

    /**
     * @return string
     */
    public function createHeadView();

    /**
     * @param object|array $row
     * @return string
     */
    public function createRowView($row);
}
