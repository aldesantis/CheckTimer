<?php

namespace CheckTimer\GameBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Level repository
 *
 * @author Alessandro Desantis <desa.alessandro@gmail.com>
 */
class LevelRepository extends EntityRepository
{
    /**
     * Returns the first level.
     *
     * @return Level|null
     */
    public function getFirst()
    {
        $level = $this->createQueryBuilder('l')
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;

        if (count($level) < 1) {
            return null;
        }

        return $level[0];
    }

    /**
     * Returns the last level.
     *
     * @return Level|null
     */
    public function getLast()
    {
        $level = $this->createQueryBuilder('l')
            ->orderBy('l.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;

        if (count($level) < 1) {
            return null;
        }

        return $level[0];
    }
}
