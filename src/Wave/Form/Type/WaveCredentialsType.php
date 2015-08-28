<?php

/**
 * Form for Wave credentials.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Skyflow\Domain\Users;

class WaveCredentialsType extends AbstractType
{
    /**
     * The skyflow current logged-in user.
     *
     * @var Users
     */
    protected $user;

    /**
     * WaveCredentialsType constructor.
     *
     * @param Users $user The skyflow current logged-in user.
     */
    public function __construct(Users $user)
    {
        $this->user = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client_id','text')
            ->add('client_secret','text')
            ->add('sandbox', 'checkbox')
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $form->setData(array(
            'client_id' => $this->user->getWaveClientId(),
            'client_secret' => $this->user->getWaveClientSecret(),
            'sandbox' => $this->user->getWaveSandbox()
        ));
    }

    public function getName()
    {
        return 'app_wave_credentials';
    }
}
