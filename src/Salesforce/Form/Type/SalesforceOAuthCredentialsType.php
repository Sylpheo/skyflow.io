<?php

/**
 * Form for Salesforce credentials.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Salesforce\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use skyflow\Form\Type\OAuthCredentialsType;

use Salesforce\Domain\SalesforceUser;

class SalesforceOAuthCredentialsType extends OAuthCredentialsType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('is_sandbox', 'checkbox', array('required' => false));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $data = $form->getData();
        $data['is_sandbox'] = $this->getUser()->getIsSandbox() ? true : false;
        $form->setData($data);
    }

    public function getName()
    {
        return 'salesforce_credentials';
    }
}
