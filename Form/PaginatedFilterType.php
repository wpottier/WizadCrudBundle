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

abstract class PaginatedFilterType extends FilterType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('page', 'number', array('required' => false))
            ->add('itemPerPage', 'choice', array(
                'choices' => array('10' => '10 items', '20' => '20 items', '40' => '40 items', '100' => '100 items')
            ))
        ;
    }

}