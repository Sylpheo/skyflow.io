<?php

/**
 * Form for OAuth credentials.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use skyflow\Domain\OAuthUser;

class OAuthCredentialsType extends AbstractType
{
    /**
     * The OAuth user.
     *
     * @var OAuthUser
     */
    private $user;

    /**
     * OAuthCredentialsType constructor.
     *
     * @param OAuthUser $user The oauth logged-in user.
     */
    public function __construct(OAuthUser $user)
    {
        $this->user = $user;
    }

    /**
     * Get the OAuth user.
     *
     * @return OAuthUser The OAuth user.
     */
    protected function getUser()
    {
        return $this->user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client_id', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('client_secret', 'text', array('attr' => array('autocomplete' => 'off')))
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $form->setData(array(
            'client_id' => $this->getUser()->getClientId(),
            'client_secret' => $this->getUser()->getClientSecret()
        ));
    }

    public function getName()
    {
        return 'oauth_credentials';
    }
}
