<?php

namespace CheckTimer\AdBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Ad repository
 *
 * @author Alessandro Desantis <desa.alessandro@gmail.com>
 */
class AdRepository extends EntityRepository
{
    private $ids = array();

    /**
     * Returns a new random ad.
     *
     * Makes sure returned ads are unique.
     *
     * @return Ad|null
     */
    public function findNewRandom()
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a, RAND() AS sortValue')
        ;

        if (!empty($this->ids)) {
            $qb->add('where', $qb->expr()->notIn('a.id', $this->ids));
        }

        $ad = $qb
            ->orderBy('sortValue')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;

        if (!empty($ad)) {
            $ad = $ad[0][0];
            $this->ids[] = $ad->getId();

            return $ad;
        }

        return null;
    }
}
