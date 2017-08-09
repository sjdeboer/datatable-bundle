<?php
namespace Sjdeboer\DataTableBundle\DataTable;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sjdeboer\DataTableBundle\Builder\TableBuilderInterface;
use Sjdeboer\DataTableBundle\ColumnType\ColumnTypeInterface;
use Sjdeboer\DataTableBundle\Exception\DataTableException;
use Sjdeboer\DataTableBundle\View\TableView;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DataTable
 * @package Sjdeboer
 */
class DataTable
{
    /** @var Request */
    private $request;

    /** @var Registry */
    private $doctrine;

    /** @var \Twig_Environment */
    private $twig;

    /** @var Router */
    private $router;

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
        $this->request = $factory->requestStack->getCurrentRequest();
        $this->doctrine = $factory->doctrine;
        $this->twig = $factory->twig;
        $this->router = $factory->router;
        $this->builder = $builder;
        $this->options = $options;

        $this->tableID = 'DataTable_' . $this->options['id'];
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return int
     */
    public function getTotal(QueryBuilder $queryBuilder)
    {
        $qb = clone $queryBuilder;

        $alias = $qb->getRootAliases();
        $qb->select('COUNT(' . $alias[0] . ') as total');

        $total = $qb->getQuery()->getResult();
        $length = count($total);

        return (int)($length > 1 ? $length : $total[0]['total']);
    }

    /**
     * @return array
     * @throws DataTableException
     */
    private function getData()
    {
        $query = $this->request->query;

        $hasRowID = array_key_exists('row_id', $this->options);
        $hasRowClass = array_key_exists('row_class', $this->options);
        $hasRowData = array_key_exists('row_data', $this->options);
        $hasRowAttr = array_key_exists('row_attr', $this->options);

        $repo = $this->doctrine->getRepository($this->options['data_class']);
        if (!($repo instanceof EntityRepository)) {
            throw new DataTableException('data_class should point to a Doctrine entity');
        }

        if (array_key_exists('query_builder', $this->options)) {
            $qb = $this->options['query_builder']($repo, $query->get('order'), $query->get('search'));
            if (!($qb instanceof QueryBuilder)) {
                throw new DataTableException('query_builder option should return a Doctrine QueryBuilder');
            }
        } else {
            $qb = $repo->createQueryBuilder('r');
        }

        $total = $this->getTotal($qb);

        if ($query->has('start') && (int)$query->get('start') > 0) {
            $qb->setFirstResult((int)$query->get('start'));
        }
        if ($query->has('length') && (int)$query->get('length') > 0) {
            $qb->setMaxResults((int)$query->get('length'));
        }

        $result = $qb->getQuery()->getResult();

        $output = [
            'draw' => ($query->has('draw') ? (int)$query->get('draw') : 1),
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

        return $this->twig->render('@SjdeboerDataTable/body.html.twig', [
            'name' => $this->tableID,
            'columnLabels' => $columnLabels,
            'tableClass' => (array_key_exists('table_class', $this->options) ? $this->options['table_class'] : ''),
        ]);
    }

    /**
     * @return string
     */
    private function createViewJS()
    {
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

        $dataTableOptions = [
            'processing' => true,
            'serverSide' => true,
            'ajax' => [
                'url' => $this->router->generate($this->request->attributes->get('_route'), $this->request->attributes->get('_route_params')),
                'data' => [
                    'datatable_id' => $this->tableID,
                ],
            ],
            'columns' => $columnOptions,
            'searchDelay' => 400,
        ];

        if (array_key_exists('datatables_options', $this->options) && is_array($this->options['datatables_options'])) {
            $dataTableOptions = array_merge($dataTableOptions, $this->options['datatables_options']);
        }

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
