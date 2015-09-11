<?php

namespace Skyflow\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

use Skyflow\Form\Type\AbstractType;

class UsersType extends AbstractType
{
    /**
     * Users type contructor.
     */
    public function __construct()
    {
        $this->setName('user');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('password', 'repeated', array(
                'type'            => 'password',
                
                'options'         => array('required' => true),
                'first_options'   => array('label' => 'Password'),
                'second_options'  => array('label' => 'Repeat password'),
            ))
            ->add('role', 'choice', array(
                'choices' => array('ROLE_ADMIN' => 'Admin', 'ROLE_USER' => 'User')
            ));
    }
}
