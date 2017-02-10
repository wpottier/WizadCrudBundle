<?php

/*
 * This file is part of the WizadCrudBundle package.
 *
 * (c) William Pottier <wpottier@allprogrammic.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wizad\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wizad\CrudBundle\Model\FilterModel;

abstract class FilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort')
            ->add('sortMode', ChoiceType::class, array(
                'choices' => array(FilterModel::SORT_ASC => 'Ascendant', FilterModel::SORT_DESC => 'Descendant')
            ));

        if(!isset($options['filterModel'])) {
            return;
        }

        if (!($options['filterModel'] instanceof FilterModel)) {
            throw new \RuntimeException('Invalid filterModel value.');
        }

        /** @var FilterModel $filterModel */
        $filterModel = $options['filterModel'];

        foreach ($filterModel->getFilters() as $property => $filter) {
            if (!$filter['formType']) {
                continue;
            }

            $builder->add($property, $filter['formType'], array_merge($filter['formOptions'] ? $filter['formOptions'] : array(), array('required' => false)));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
          ->setDefaults(array(
            'csrf_protection' => false,
            'method'          => 'get',
            'filterModel'     => null
          ));
    }

    public function getName()
    {
        return '';
    }
}