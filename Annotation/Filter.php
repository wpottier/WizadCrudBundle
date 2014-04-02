<?php

/*
 * This file is part of the WizadCrudBundle package.
 *
 * (c) William Pottier <wpottier@allprogrammic.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wizad\CrudBundle\Annotation;

use Doctrine\ORM\Mapping\Annotation;

/**
 * Class Filter
 *
 * @Annotation()
 */
class Filter
{
    public $formType;

    public $formOptions;

    public function __construct()
    {

    }
}