<?php

/*
 * This file is part of the WizadCrudBundle package.
 *
 * (c) William Pottier <wpottier@allprogrammic.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wizad\CrudBundle\Manager;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Wizad\CrudBundle\Model\FilterModel;


abstract class EntityFilterManager implements FilterManagerInterface
{
    /**
     * @return EntityRepository
     */
    public abstract function getEntityRepository();

    public function findByPrimaryKey($primaryKey)
    {
        return $this->getEntityRepository()->find($primaryKey);
    }

    public function count(FilterModel $filter)
    {
        $qb = $this->createQueryByFilter($filter);
        $qb->select('COUNT(entity)')->setMaxResults(null)->setFirstResult(null);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param FilterModel $filter
     *
     * @return mixed
     */
    public function findByFilter(FilterModel $filter)
    {
        $query = $this->createQueryByFilter($filter);

        return $query->getQuery()->execute();
    }

    /**
     * @param FilterModel $filter
     *
     * @return QueryBuilder
     */
    public function createQueryByFilter(FilterModel $filter)
    {
        $qb = $this->getEntityRepository()->createQueryBuilder('entity');
        $this->applyFilter($qb, $filter);

        return $qb;
    }

    /**
     *
     * @param \Doctrine\ORM\QueryBuilder $qb
     * @param FilterModel                $filter
     *
     * @return QueryBuilder
     */
    protected function applyFilter(QueryBuilder $qb, FilterModel $filter)
    {
        foreach ($filter->getFilters() as $propertyName => $property) {

            $value       = $filter->getFilterValue($property['property']);
            $queryMethod = sprintf('filter%s', ucfirst($propertyName));

            if (method_exists($this, $queryMethod)) {
                $this->$queryMethod($qb, $filter, $value);
            } else {
                if ($value !== null) {
                    if (!is_array($value)) {
                        $qb
                            ->andWhere(
                                $qb->expr()->like(sprintf('entity.%s', $propertyName), sprintf(':%s', $propertyName))
                            )
                            ->setParameter($propertyName, '%' . $value . '%');
                    } elseif (!empty($value)) {
                        $qb
                            ->andWhere(
                                $qb->expr()->in(sprintf('entity.%s', $propertyName), sprintf(':%s', $propertyName))
                            )
                            ->setParameter($propertyName, $value);
                    }
                }
            }
        }

        if (strlen($filter->getSort()) > 0) {
            $qb->orderBy(sprintf('entity.%s', $filter->getSort()), $filter->getSortMode());
        }

        return $qb;
    }
}
