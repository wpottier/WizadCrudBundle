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

use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder;
use Wizad\CrudBundle\Model\FilterModel;


abstract class DocumentFilterManager implements FilterManagerInterface
{
    /**
     * @return DocumentRepository
     */
    public abstract function getDocumentRepository();

    public function findByPrimaryKey($primaryKey)
    {
        return $this->getDocumentRepository()->find($primaryKey);
    }

    public function count(FilterModel $filter)
    {
        $qb = $this->createQueryByFilter($filter);
        
        //By default mongo set the limit to 10 so we have to remove it before count 
        $qb->limit(0);
        $qb->count();

        /** @var Cursor $cursor */
        $cursor = $qb->getQuery()->execute();
        return $cursor;
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
    protected function applyFilter(Builder $qb, FilterModel $filter)
    {
        foreach ($filter->getFilters() as $propertyName => $property) {

            $value       = $filter->getFilterValue($property['property']);
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

        if (strlen($filter->getSort()) > 0) {
            $qb->sort($filter->getSort(), $filter->getSortMode());
        }

        return $qb;
    }
}
