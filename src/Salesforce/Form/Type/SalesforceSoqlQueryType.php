<?php

/**
 * Form for Salesforce SOQL query.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form for Salesforce SOQL query.
 */
class SalesforceSoqlQueryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Request', 'textarea', array(
                'attr' => array('cols' => '100', 'rows' => '3')
            ))
        ;
    }

    public function getName()
    {
        return 'salesforce_soql';
    }
}
