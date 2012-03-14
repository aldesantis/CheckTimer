<?php

namespace CheckTimer\AdBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;

/**
 * Ad extension
 *
 * @author Alessandro Desantis <desa.alessandro@gmail.com>
 */
class AdExtension extends \Twig_Extension
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            'show_ad' => new \Twig_Function_Method($this, 'getAdCode'),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ad';
    }

    /**
     * Returns an ad's HTML code, or null if no ads are available.
     *
     * @return string|null
     */
    public function getAdCode()
    {
        $repo = $this->em->getRepository('CheckTimerAdBundle:Ad');

        $ad = $repo->findNewRandom();

        if ($ad !== null) {
            return $ad->getCode();
        }

        return null;
    }
}
