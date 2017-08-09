<?php
namespace Sjdeboer\DataTableBundle\View;

/**
 * Class TableView
 * @package Sjdeboer\DataTable
 */
class TableView
{
    /** @var string */
    private $body;

    /** @var string */
    private $js;

    /** @var array */
    private $json;

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return TableView
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     * @param string $js
     * @return TableView
     */
    public function setJs($js)
    {
        $this->js = $js;
        return $this;
    }

    /**
     * @return array
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param array $json
     * @return TableView
     */
    public function setJson($json)
    {
        $this->json = $json;
        return $this;
    }
}
