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

use Wizad\CrudBundle\Model\FilterModel;
use Wizad\CrudBundle\Model\PaginatedFilterModel;

abstract class EntityPaginatedFilterManager extends EntityFilterManager
{
    public function createQueryByFilter(FilterModel $filter)
    {
        if (!$filter instanceof PaginatedFilterModel) {
            throw new \RuntimeException();
        }

        /** @var PaginatedFilterModel $filter */

        $qb = parent::createQueryByFilter($filter);

        $qb->setMaxResults($filter->getItemPerPage());
        $qb->setFirstResult(($filter->getPage() - 1) * $filter->getItemPerPage());

        return $qb;
    }
}