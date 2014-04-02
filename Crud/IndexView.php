<?php

namespace Wizad\CrudBundle\Crud;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Wizad\CrudBundle\Model\FilterModel;
use Wizad\CrudBundle\Model\PaginatedFilterModel;

class IndexView
{
    /**
     * @var FilterModel
     */
    protected $filterModel;

    /**
     * @var FormView
     */
    protected $filterForm;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var int
     */
    protected $total;

    /**
     * @var array
     */
    protected $columns;

    protected $linkClosure;

    public function __construct(FilterModel $filterModel, FormView $filterForm, $total, $items, $linkClosure)
    {
        $this->filterForm  = $filterForm;
        $this->filterModel = $filterModel;
        $this->items       = $items;
        $this->total       = $total;
        $this->linkClosure = $linkClosure;
        $this->columns     = array();
    }

    public function linkView($item)
    {
        return call_user_func($this->linkClosure, $item);
    }

    /**
     * @return \Symfony\Component\Form\FormView
     */
    public function getFilterForm()
    {
        return $this->filterForm;
    }

    /**
     * @return \Wizad\CrudBundle\Model\FilterModel
     */
    public function getFilterModel()
    {
        return $this->filterModel;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param $name     string
     * @param $label    string
     * @param $sortable boolean
     *
     * @return $this
     */
    public function addColumn($name, $label, $options = array())
    {
        $this->columns[$name] = array(
            'label'    => $label,
            'sortable' => isset($options['sortable']) ? $options['sortable'] : false,
            'viewable' => isset($options['viewable']) ? $options['viewable'] : false
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return int
     */
    public function getPageNumber()
    {
        if(!$this->getFilterModel() instanceof PaginatedFilterModel) {
            return 0;
        }

        /** @var PaginatedFilterModel $filterModel */
        $filterModel = $this->getFilterModel();

        $count = (int)($this->total / $filterModel->getItemPerPage());
        if ($this->total % $filterModel->getItemPerPage() > 0) {
            $count++;
        }

        return $count;
    }
} 