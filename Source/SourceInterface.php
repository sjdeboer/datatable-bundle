<?php
namespace Sjdeboer\DataTableBundle\Source;

/**
 * Interface SourceInterface
 * @package Sjdeboer\DataTableBundle\Source
 */
interface SourceInterface
{
    /**
     * @param callable|null $filter
     */
    public function setFilter(callable $filter = null);

    /**
     * @return int
     */
    public function getTotal();

    /**
     * @return array
     */
    public function getData();
}
