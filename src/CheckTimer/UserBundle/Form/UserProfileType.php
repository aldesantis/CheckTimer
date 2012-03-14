<?php

namespace CheckTimer\UserBundle\Form;

use Symfony\Component\Form\FormBuilder;

class UserProfileType extends AbstractUserType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('password', 'repeated', array(
                'type'            => 'password',
                'invalid_message' => '',
                'options'         => array(
                    'label' => 'Password:',
                ),
            ))
            ->add('email', 'email', array(
                'read_only' => true,
                'label'     => 'Email:',
            ))
            ->add('newEmail', 'repeated', array(
                'type'            => 'email',
                'invalid_message' => 'Le due email non coincidono.',
                'required'        => false,
                'options'         => array(
                    'label' => 'Nuova email:',
                ),
            ))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'user_profile';
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'validation_groups' => array('Default', 'Profile'),
        );
    }
}
