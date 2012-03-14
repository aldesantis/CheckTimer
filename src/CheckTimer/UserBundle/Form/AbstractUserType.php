<?php

namespace CheckTimer\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

abstract class AbstractUserType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'user';
    }
}
