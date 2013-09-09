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
use Symfony\Component\Form\FormBuilderInterface;
use Wizad\CrudBundle\Model\FilterModel;

abstract class FilterType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('sort')
            ->add('sortMode', 'choice', array(
                'choices' => array(FilterModel::SORT_ASC => 'Ascendant', FilterModel::SORT_DESC => 'Descendant')
            ))
        ;
    }

}