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

use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use Wizad\CrudBundle\Model\FilterModel;


abstract class DocumentFilterManager
{
    /**
     * @return DocumentRepository
     */
    public abstract function getDocumentRepository();

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
     * @return Builder
     */
    public function createQueryByFilter(FilterModel $filter)
    {
        $qb = $this->getDocumentRepository()->createQueryBuilder('entity');
        $this->applyFilter($qb, $filter);

        return $qb;
    }

    /**
     * @param Builder     $qb
     * @param FilterModel $filter
     *
     * @return Builder
     */
    public function applyFilter(Builder $qb, FilterModel $filter)
    {
        foreach ($filter->getFilters() as $propertyName => $property) {

            $value       = $filter->getFilterValue($propertyName);
            $queryMethod = sprintf('filter%s', ucfirst($propertyName));

            if (method_exists($this, $queryMethod)) {
                $this->$queryMethod($qb, $filter, $value);
            } else {
                if ($value !== null) {
                    if (!is_array($value)) {
                        $qb->field($propertyName)->equals(new \MongoRegex(sprintf('/%s/i', $value)));
                    } elseif (!empty($value)) {
                        $qb->field($propertyName)->in($value);
                    }
                }
            }
        }
        
        if(strlen($filter->getSort()) > 0) {
            $qb->sort($filter->getSort(), $filter->getSortMode());
        }

        return $qb;
    }
}
