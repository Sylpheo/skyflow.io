<?php

/**
 * Form for Wave credentials.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class WaveCredentialsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client_id','text')
            ->add('client_secret','text')
            ->add('sandbox', 'checkbox')
        ;
    }

    public function getName()
    {
        return 'app_wave_credentials';
    }
}