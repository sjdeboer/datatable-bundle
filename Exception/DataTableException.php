<?php
namespace Sjdeboer\DataTableBundle\Exception;

use Throwable;

/**
 * Class DataTableException
 * @package Sjdeboer\DataTable
 */
class DataTableException extends \Exception
{
    /**
     * @inheritdoc
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
