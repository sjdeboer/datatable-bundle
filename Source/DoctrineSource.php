<?php
namespace Sjdeboer\DataTableBundle\Source;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Sjdeboer\DataTableBundle\Exception\DataTableException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DoctrineSource
 * @package Sjdeboer\DataTableBundle\Source
 */
class DoctrineSource implements SourceInterface
{
    /** @var EntityRepository */
    private $repository;

    /** @var callable|null */
    private $filter;

    /** @var Request */
    private $request;

    /**
     * DoctrineSource constructor.
     * @param EntityRepository $repository
     * @param callable|null $filter
     * @throws DataTableException
     */
    public function __construct(EntityRepository $repository, callable $filter = null)
    {
        if (!($repository instanceof EntityRepository)) {
            throw new DataTableException('repository should be a Doctrine EntityRepository');
        }
        if ($filter !== null && !is_callable($filter)) {
            throw new DataTableException('filter should be a closure');
        }

        $this->repository = $repository;
        $this->filter = $filter;
        $this->request = Request::createFromGlobals();
    }

    /**
     * @param callable|null $filter
     * @throws DataTableException
     */
    public function setFilter(callable $filter = null) {
        if ($filter !== null && !is_callable($filter)) {
            throw new DataTableException('filter should be a closure');
        }
        $this->filter = $filter;
    }

    /**
     * @inheritdoc
     */
    public function getTotal()
    {
        $qb = $this->repository->createQueryBuilder('t');

        $qb->select('COUNT(t) as total');

        $total = $qb->getQuery()->getResult();
        $length = count($total);

        return (int)($length > 1 ? $length : $total[0]['total']);
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if (is_callable($this->filter)) {
            $qbCallable = $this->filter;
            $qb = $qbCallable($this->repository, $this->request->query->get('order'), $this->request->query->get('search'));
            if (!($qb instanceof QueryBuilder)) {
                throw new DataTableException('query_builder option should return a Doctrine QueryBuilder');
            }
        } else {
            $qb = $this->repository->createQueryBuilder('r');
        }

        if ($this->request->query->has('start') && (int)$this->request->query->get('start') > 0) {
            $qb->setFirstResult((int)$this->request->query->get('start'));
        }
        if ($this->request->query->has('length') && (int)$this->request->query->get('length') > 0) {
            $qb->setMaxResults((int)$this->request->query->get('length'));
        }

        return $qb->getQuery()->getResult();
    }
}
