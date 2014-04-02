<?php

/*
 * This file is part of the WizadCrudBundle package.
 *
 * (c) William Pottier <wpottier@allprogrammic.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wizad\CrudBundle\Model;

use Doctrine\Common\Annotations\Reader;
use Wizad\CrudBundle\Annotation\Filter;

class FilterModel
{
    const ANNOTATION_CLASS = 'Wizad\\CrudBundle\\Annotation\\Filter';

    const SORT_ASC  = 'asc';
    const SORT_DESC = 'desc';

    private $reader;

    private $filters = array();

    private $sort;

    private $sortMode;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;

        $this->extractAnnotation();
    }


    /**
     * @param mixed $sort
     *
     * @return FilterModel
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param mixed $sortMode
     *
     * @return FilterModel
     */
    public function setSortMode($sortMode)
    {
        $this->sortMode = $sortMode;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSortMode()
    {
        return $this->sortMode;
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getFilterValue($property)
    {
        if(is_string($property)) {
            $property = $this->filters[$property]['property'];
        }

        /** @var \ReflectionProperty $property */

        $property->setAccessible(true);
        $value = $property->getValue($this);

        return $value;
    }

    /**
     * @return mixed
     */
    public function urlizeSort()
    {
        return $this->sort;
    }

    /**
     * @return mixed
     */
    public function urlizeSortMode()
    {
        return $this->sortMode;
    }

    /**
     *
     */
    private function extractAnnotation()
    {
        $reflectionClass = new \ReflectionClass(get_called_class());
        $properties      = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            if ($annotation = $this->reader->getPropertyAnnotation($property, self::ANNOTATION_CLASS)) {
                /** @var Filter $annotation */

                $this->filters[$property->getName()] = array(
                    'property'    => $property,
                    'formType'    => $annotation->formType,
                    'formOptions' => $annotation->formOptions
                );
            }
        }
    }


}