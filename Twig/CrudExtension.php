<?php

/*
 * This file is part of the WizadCrudBundle package.
 *
 * (c) William Pottier <wpottier@allprogrammic.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wizad\CrudBundle\Twig;

use Symfony\Bridge\Twig\Form\TwigRendererInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Wizad\CrudBundle\Crud\IndexView;
use Wizad\CrudBundle\Model\PaginatedFilterModel;

class CrudExtension extends \Twig_Extension
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "wizad_crud";
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('paginator', array($this, 'paginator'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('oldPaginator', array($this, 'oldPaginator'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('sortPath', array($this, 'sortPath'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('sortIndicator', array($this, 'sortIndicator'), array('is_safe' => array('html'))),
        );
    }

    public function sortIndicator(PaginatedFilterModel $filter, FormView $formFilter, $sort, $sortUpHtml = 'up', $sortDownHtml = 'down')
    {
        $filtersValue = $this->extractFilter($filter, $formFilter);
        if (isset($filtersValue[$formFilter['sort']->vars['full_name']]) && $filtersValue[$formFilter['sort']->vars['full_name']] == $sort) {
            return $filtersValue[$formFilter['sortMode']->vars['full_name']] == 'asc' ? $sortUpHtml : $sortDownHtml;
        }

        return null;
    }

    public function sortPath(PaginatedFilterModel $filter, FormView $formFilter, $sort)
    {
        $filtersValue = $this->extractFilter($filter, $formFilter);

        if (isset($filtersValue[$formFilter['sort']->vars['full_name']]) && $filtersValue[$formFilter['sort']->vars['full_name']] == $sort) {
            $filtersValue[$formFilter['sortMode']->vars['full_name']] = $filtersValue[$formFilter['sortMode']->vars['full_name']] == 'asc' ? 'desc' : 'asc';
        } else {
            $filtersValue[$formFilter['sort']->vars['full_name']]     = $sort;
            $filtersValue[$formFilter['sortMode']->vars['full_name']] = 'asc';
        }

        return $this->generateUrl($filtersValue, $filtersValue[$formFilter['page']->vars['full_name']], $formFilter['page']->vars['full_name']);
    }

    public function oldPaginator(PaginatedFilterModel $filter, FormView $formFilter, $items)
    {
        // Create fake crud for compatibility
        $crud = new IndexView($filter, $formFilter, $filter->getTotal(), $items, function() {});
        return $this->paginator($crud);
    }

    public function paginator(IndexView $crud)
    {
        /** @var PaginatedFilterModel $filterModel */
        $filterModel = $crud->getFilterModel();
        $filterForm = $crud->getFilterForm();

        $pageNumber = $crud->getPageNumber();
        $page = $filterModel->getPage();

        if ($pageNumber <= 1) {
            return '';
        }

        $filtersValue = $this->extractFilter($filterModel, $filterForm);

        $paginator = '<ul class="pagination">';

        // Create left arrow
        $paginator .= '<li ' . ($filterModel->getPage() == 1 ? 'class="disabled"' : '') . '><a href="' . $this->generateUrl($filtersValue, 1, $filterForm['page']->vars['full_name']) . '">««</a></li>';
        $paginator .= '<li ' . ($filterModel->getPage() == 1 ? 'class="disabled"' : '') . '><a href="' . $this->generateUrl($filtersValue, ($filterModel->getPage() > 1 ? $filterModel->getPage() - 1 : 1), $filterForm['page']->vars['full_name']) . '">«</a></li>';

        if ($pageNumber < 7) {
            for ($i = 1; $i <= $pageNumber; $i++) {
                $paginator .= '<li' . ($i == $filterModel->getPage() ? ' class="active"' : '') . '><a href="' . $this->generateUrl($filtersValue, $i, $filterForm['page']->vars['full_name']) . '">' . $i . '</a></li>';
            }
        } else {
            $start = $filterModel->getPage() < 4 ? 1 : $filterModel->getPage() - 3;

            if ($start + 7 > $pageNumber) {
                $start -= (($start + 7) - $pageNumber) - 1;
            }

            for ($i = 0; $i < 7; $i++) {
                $position = $i + $start;

                if ($position > $pageNumber) {
                    break;
                }

                $paginator .= '<li ' . ($position == $page ? 'class="active"' : '') . '><a href="' . $this->generateUrl($filtersValue, $position, $filterForm['page']->vars['full_name']) . '">' . $position . '</a></li>';
            }
        }

        // Create right arrow
        $paginator .= '<li' . ($page == $pageNumber ? ' class="disabled"' : '') . '><a href="' . $this->generateUrl($filtersValue, ($page < $pageNumber ? $page + 1 : $pageNumber), $filterForm['page']->vars['full_name']) . '">»</a></li>';
        $paginator .= '<li' . ($page == $pageNumber ? ' class="disabled"' : '') . '><a href="' . $this->generateUrl($filtersValue, $pageNumber, $filterForm['page']->vars['full_name']) . '">»»</a></li>';

        $paginator .= '</ul>';

        return $paginator;
    }

    private function extractFilter(PaginatedFilterModel $filter, FormView $formFilter)
    {
        $filtersValue = array();
        foreach ($formFilter as $name => $item) {

            $method = sprintf('urlize%s', ucfirst($name));
            if (method_exists($filter, $method)) {
                $value = $filter->$method();
            } else {
                $value = $filter->getFilterValue($name);
            }

            $key = $item->vars['full_name'];

            if (is_array($value)) {

                $value = array_merge($value);

                if (array_keys($value) === range(0, count($value) - 1)) {
                    $key = substr($key, 0, -2);
                }
            }

            if($value === null)
                continue;

            /** @var FormView $item */
            $filtersValue[$key] = $value;
        }

        return $filtersValue;
    }

    private function generateUrl($args, $page, $pageAttributeName)
    {
        return $this->container->get('router')->generate($this->container->get('request')->get('_route'), array_merge($args, array($pageAttributeName => $page)));
    }

}
