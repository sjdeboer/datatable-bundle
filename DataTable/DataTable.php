<?php
namespace Sjdeboer\DataTableBundle\DataTable;

use Sjdeboer\DataTableBundle\Builder\TableBuilderInterface;
use Sjdeboer\DataTableBundle\ColumnType\ColumnTypeInterface;
use Sjdeboer\DataTableBundle\Exception\DataTableException;
use Sjdeboer\DataTableBundle\Source\SourceInterface;
use Sjdeboer\DataTableBundle\View\TableView;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DataTable
 * @package Sjdeboer
 */
class DataTable
{
    /** @var Request */
    private $request;

    /** @var \Twig_Environment */
    private $twig;

    /** @var array */
    private $config;

    /** @var TableBuilderInterface */
    private $builder;

    /** @var array */
    private $options;

    /** @var string */
    private $tableID;

    /**
     * DataTable constructor.
     * @param DataTableFactory $factory
     * @param TableBuilderInterface $builder
     * @param array $options
     */
    public function __construct(DataTableFactory $factory, TableBuilderInterface $builder, array $options = [])
    {
        $this->request = Request::createFromGlobals();
        $this->twig = $factory->twig;
        $this->config = $factory->config;
        $this->builder = $builder;
        $this->options = $options;

        $this->tableID = 'DataTable_' . $this->options['id'];
    }

    /**
     * @return array
     * @throws DataTableException
     */
    private function getData()
    {
        if (!($this->options['data_source'] instanceof SourceInterface)) {
            throw new DataTableException('');
        }

        $hasRowID = array_key_exists('row_id', $this->options);
        $hasRowClass = array_key_exists('row_class', $this->options);
        $hasRowData = array_key_exists('row_data', $this->options);
        $hasRowAttr = array_key_exists('row_attr', $this->options);

        $total = $this->options['data_source']->getTotal();
        $result = $this->options['data_source']->getData();

        $output = [
            'draw' => ($this->request->query->has('draw') ? (int)$this->request->query->get('draw') : 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => [],
        ];
        foreach ($result as $row) {
            $rowData = [];
            $i = 0;
            foreach ($this->builder as $column) {
                if (!($column instanceof ColumnTypeInterface)) {
                    continue;
                }
                $rowData[(string)$i] = trim($column->createRowView($row));
                $i++;
            }

            if ($hasRowID) {
                $rowData['DT_RowId'] = (string)$this->options['row_id']($row);
            }
            if ($hasRowClass) {
                $rowData['DT_RowClass'] = (string)$this->options['row_class']($row);
            }
            if ($hasRowData) {
                $rowData['DT_RowData'] = (array)$this->options['row_data']($row);
            }
            if ($hasRowAttr) {
                $rowData['DT_RowAttr'] = (array)$this->options['row_attr']($row);
            }

            $output['data'][] = $rowData;
        }

        return $output;
    }

    /**
     * @return string
     */
    private function createViewBody()
    {
        $columnLabels = [];
        foreach ($this->builder as $column) {
            if (!($column instanceof ColumnTypeInterface)) {
                continue;
            }
            $columnLabels[] = $column->createHeadView();
        }

        $tableClass = '';
        if (array_key_exists('table_class', $this->options)) {
            $tableClass = $this->options['table_class'];
        } elseif (array_key_exists('default_table_class', $this->config)) {
            $tableClass = $this->config['default_table_class'];
        }

        return $this->twig->render('@SjdeboerDataTable/body.html.twig', [
            'name' => $this->tableID,
            'columnLabels' => $columnLabels,
            'tableClass' => $tableClass,
        ]);
    }

    /**
     * @return string
     */
    private function createViewJS()
    {
        $dataTableOptions = [];
        if (array_key_exists('default_datatables_options', $this->config) && is_array($this->config['default_datatables_options'])) {
            $dataTableOptions = $this->config['default_datatables_options'];
        }
        if (array_key_exists('datatables_options', $this->options) && is_array($this->options['datatables_options'])) {
            $dataTableOptions = array_replace_recursive($dataTableOptions, $this->options['datatables_options']);
        }

        $columnOptions = [];
        $i = 0;
        foreach ($this->builder as $column) {
            if (!($column instanceof ColumnTypeInterface)) {
                continue;
            }
            $options = $column->getOptions();
            $options['column_options']['data'] = (string)$i;
            $columnOptions[] = $options['column_options'];
            $i++;
        }

        $dataTableOptions = array_replace_recursive($dataTableOptions, [
            'processing' => true,
            'serverSide' => true,
            'ajax' => [
                'url' => $this->request->getUri(),
                'data' => [
                    'datatable_id' => $this->tableID,
                ],
            ],
            'columns' => $columnOptions,
        ]);

        $output = $this->twig->render('@SjdeboerDataTable/js.html.twig', [
            'name' => $this->tableID,
            'options' => $dataTableOptions,
        ]);

        return $output;
    }

    /**
     * @return TableView
     */
    public function createView()
    {
        $view = new TableView();

        if ($this->request->isXmlHttpRequest()) {
            $generatedID = strpos($this->options['id'], 'auto_') === 0;
            if ($generatedID || (!$generatedID && $this->request->query->has('datatable_id') && $this->request->query->get('datatable_id') === $this->tableID)) {
                $view->setJson($this->getData());
                return $view;
            }
        }

        $columnLabels = [];
        $columnOptions = [];
        foreach ($this->builder as $column) {
            if (!($column instanceof ColumnTypeInterface)) {
                continue;
            }
            $columnLabels[] = $column->createHeadView();

            $options = $column->getOptions();
            $columnOptions[] = $options['column_options'];
        }

        $view
            ->setBody($this->createViewBody())
            ->setJs($this->createViewJS());

        return $view;
    }
}
