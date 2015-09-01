<?php

/**
 * Form for Salesforce SOQL query.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

use skyflow\Form\Type\AbstractType;

/**
 * Form for Salesforce SOQL query.
 */
class SalesforceSoqlQueryType extends AbstractType
{
    /**
     * SalesforceSoqlQueryType constructor.
     */
    public function __construct()
    {
        $this->setName('salesforce_soql');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Request', 'textarea', array(
                'attr' => array('cols' => '100', 'rows' => '3')
            ))
        ;
    }
}
