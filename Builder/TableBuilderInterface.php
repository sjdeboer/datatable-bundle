<?php
namespace Sjdeboer\DataTableBundle\Builder;

use Sjdeboer\DataTableBundle\DataTable\DataTable;

/**
 * Interface TableBuilderInterface
 */
interface TableBuilderInterface extends \Iterator
{
    /**
     * @param string $type
     * @param array $options
     * @return TableBuilderInterface
     */
    public function add($type = null, array $options = array());

    /**
     * @return DataTable
     */
    public function getTable();
}
