<?php

/**
 * Form for Salesforce credentials.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Salesforce\Form\Type\SalesforceOAuthCredentialsType;

class WaveCredentialsType extends SalesforceOAuthCredentialsType
{
    public function getName()
    {
        return 'wave_credentials';
    }
}
