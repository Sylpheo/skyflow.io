<?php

/**
 * Form for OAuth credentials.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace skyflow\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use skyflow\Domain\OAuthUser;
use skyflow\Form\Type\AbstractType;

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
        $this->setName('oauth_credentials');
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

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client_id', 'text', array('attr' => array('autocomplete' => 'off')))
            ->add('client_secret', 'text', array('attr' => array('autocomplete' => 'off')))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!$form->isSubmitted()) {
            $form->setData(array(
                'client_id' => $this->getUser()->getClientId(),
                'client_secret' => $this->getUser()->getClientSecret()
            ));
        }
    }
}
