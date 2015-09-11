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

use Skyflow\Form\Type\OAuthCredentialsType;
use Skyflow\Domain\OAuthUser;

use Salesforce\Domain\SalesforceUser;

class SalesforceOAuthCredentialsType extends OAuthCredentialsType
{
    /**
     * {@inherit}
     */
    public function __construct(OAuthUser $user)
    {
        parent::__construct($user);
        $this->setName('salesforce_credentials');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('is_sandbox', 'checkbox', array('required' => false));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        if (!$form->isSubmitted()) {
            $data = $form->getData();
            $data['is_sandbox'] = $this->getUser()->getIsSandbox() ? true : false;
            $form->setData($data);
        }
    }
}
