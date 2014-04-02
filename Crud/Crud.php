<?php

namespace Wizad\CrudBundle\Crud;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Wizad\CrudBundle\Form\FilterType;
use Wizad\CrudBundle\Manager\FilterManagerInterface;
use Wizad\CrudBundle\Model\FilterModel;

abstract class Crud implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    public function buildIndex()
    {
        $filterModel = $this->getFilterModel();

        $filterForm = $this->container->get('form.factory')->create($this->getFilterFormType(), $filterModel, array(
            'action' => $this->container->get('router')->generate($this->getMasterRequest()->get('_route'), $this->getMasterRequest()->get('_route_params')),
            'filterModel' => $filterModel
        ));
        $filterForm->handleRequest($this->getMasterRequest());

        $context = $this;
        $view = new IndexView(
            $filterModel,
            $filterForm->createView(),
            $this->getItemManager()->count($filterModel),
            $this->getItemManager()->findByFilter($filterModel),
            function($item) use ($context) { return $context->generateViewLink($item); }
        );
        return $view;
    }

    public function buildRead($primaryKey)
    {
        $this->getItemManager()->findByPrimaryKey($primaryKey);
    }

    /**
     * @return FilterManagerInterface
     */
    protected abstract function getItemManager();

    /**
     * @return FilterModel
     */
    protected abstract function getFilterModel();

    /**
     * @return FilterType
     */
    protected abstract function getFilterFormType();

    protected abstract function generateIndexLink();

    protected abstract function generateViewLink($item);

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getMasterRequest()
    {
        return $this->container->get('request_stack')->getMasterRequest();
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string         $route         The name of the route
     * @param mixed          $parameters    An array of parameters
     * @param Boolean|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }
} 