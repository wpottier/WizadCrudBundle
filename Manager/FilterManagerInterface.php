<?php

namespace Wizad\CrudBundle\Manager;

use Wizad\CrudBundle\Model\FilterModel;

interface FilterManagerInterface
{
    public function count(FilterModel $filter);

    public function findByFilter(FilterModel $filter);

    public function findByPrimaryKey($primaryKey);

    public function createQueryByFilter(FilterModel $filter);
} 