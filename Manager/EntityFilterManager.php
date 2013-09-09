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


abstract class EntityFilterManager
{
    /**
     * @return EntityRepository
     */
    public abstract function getEntityRepository();

    public function findByFilter(FilterModel $filter)
    {
        $query = $this->createQueryByFilter($filter);

        return $query->getQuery()->execute();
    }

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
    public function applyFilter(QueryBuilder $qb, FilterModel $filter)
    {
        foreach ($filter->getFilters() as $propertyName => $property) {

            $value       = $filter->getFilterValue($propertyName);
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

        return $qb;
    }
}