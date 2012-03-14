<?php

namespace CheckTimer\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * User repository
 *
 * @author Alessandro Desantis <desa.alessandro@gmail.com>
 */
class UserRepository extends EntityRepository
{
    /**
     * Finds a user by username or by email.
     *
     * @param string $id
     *
     * @return User|null
     */
    public function findOneByUsernameOrEmail($id)
    {
        $qb = $this->createQueryBuilder('u');

        $query = $qb
            ->where($qb->expr()->orx(
                $qb->expr()->eq('u.username', ':id'),
                $qb->expr()->eq('u.email', ':id')
            ))
            ->setParameters(array('id' => $id))
            ->setMaxResults(1)
            ->getQuery()
        ;

        $user = $query->getResult();

        if (count($user) == 1) {
            return $user[0];
        }

        return null;
    }
}
