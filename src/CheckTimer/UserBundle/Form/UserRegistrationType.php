<?php

namespace CheckTimer\UserBundle\Form;

use Symfony\Component\Form\FormBuilder;

class UserRegistrationType extends AbstractUserType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('username', 'text', array(
                'label' => 'Username:',
            ))
            ->add('password', 'repeated', array(
                'type'            => 'password',
                'invalid_message' => '',
                'options'         => array(
                    'label' => 'Password:',
                ),
            ))
            ->add('email', 'repeated', array(
                'type'            => 'email',
                'invalid_message' => '',
                'options'         => array(
                    'label' => 'Email:',
                ),
            ))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'user_registration';
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'validation_groups' => array('Default', 'Registration'),
        );
    }
}
