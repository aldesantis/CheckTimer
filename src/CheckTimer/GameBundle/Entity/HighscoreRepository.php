<?php

namespace CheckTimer\GameBundle\Entity;

use Doctrine\ORM\EntityRepository;
use CheckTimer\UserBundle\Entity\User;

/**
 * Highscore repository
 *
 * @author Alessandro Desantis <desa.alessandro@gmail.com>
 */
class HighscoreRepository extends EntityRepository
{
    /**
     * Returns the last 100 highscores ordered by level (and sub-ordered by
     * time).
     *
     * @return array
     */
    public function getLatestOrdered()
    {
        $result = $this->createQueryBuilder('h')
            ->setMaxResults(100)
            ->getQuery()
            ->getResult()
        ;

        return $this->orderHighscores($result);
    }

    /**
     * Returns the statistics for the given user.
     *
     * @param User $user
     *
     * @return array|bool The stats or FALSE if the user doesn't have highscores
     */
    public function getStatistics(User $user)
    {
        $all = $user->getHighscores();

        if (count($all) == 0) {
            return false;
        }

        $avg = 0;
        foreach ($all as $highscore) {
            $avg += $highscore->getTime();
        }
        $avg /= count($all);

        $qb = $this->_em->createQueryBuilder();
        $query = $qb
            ->select('MAX(h.level) AS maxLevel')
            ->from('CheckTimerGameBundle:Highscore', 'h')
            ->where($qb->expr()->eq('h.user', $user->getId()))
            ->getQuery()
        ;
        $result = $query->getSingleResult();
        $maxLevel = $result['maxLevel'];

        $qb = $this->_em->createQueryBuilder();
        $query = $qb
            ->select('MIN(h.time) AS minTime')
            ->from('CheckTimerGameBundle:Highscore', 'h')
            ->where($qb->expr()->andx(
                $qb->expr()->eq('h.user', $user->getId()),
                $qb->expr()->eq('h.level', $maxLevel)
            ))
            ->getQuery()
        ;
        $result = $query->getSingleResult();
        $minTime = $result['minTime'];

        $qb = $this->_em->createQueryBuilder();
        $query = $qb
            ->select('MIN(h.level) AS minLevel')
            ->from('CheckTimerGameBundle:Highscore', 'h')
            ->where($qb->expr()->eq('h.user', $user->getId()))
            ->getQuery()
        ;
        $result = $query->getSingleResult();
        $minLevel = $result['minLevel'];

        $qb = $this->_em->createQueryBuilder();
        $query = $qb
            ->select('MAX(h.time) AS maxTime')
            ->from('CheckTimerGameBundle:Highscore', 'h')
            ->where($qb->expr()->andx(
                $qb->expr()->eq('h.user', $user->getId()),
                $qb->expr()->eq('h.level', $minLevel)
            ))
            ->getQuery()
        ;
        $result = $query->getSingleResult();
        $maxTime = $result['maxTime'];

        return array(
            'avg'        => $avg,
            'highscores' => count($all),
            'best'       => array(
                'level' => $maxLevel,
                'time'  => $minTime,
            ),
            'worst'      => array(
                'level' => $minLevel,
                'time'  => $maxTime,
            ),
        );
    }

    /**
     * Orders a set of highscores.
     *
     * @param array|\Traversable $result
     *
     * @return array
     */
    public function orderHighscores($result)
    {
        $temp = $this->splitByLevel($result);

        foreach ($temp as $k => $highscores) {
            $temp[$k] = $this->orderByTime($highscores);
        }

        $highscores = array();
        foreach ($temp as $subtemp) {
            $highscores = array_merge($highscores, $subtemp);
        }

        return $highscores;
    }

    /**
     * Orders the given highscores by time.
     *
     * @param array|\Traversable $result
     *
     * @return array
     */
    public function orderByTime($result)
    {
        usort($result, function($a, $b) {
            if ($a->getTime() == $b->getTime()) {
                return 0;
            }

            if ($a->getTime() > $b->getTime()) {
                return 1;
            }

            return -1;
        });

        return $result;
    }

    /**
     * Splits the given highscores by level ID and returns them reverse ordered.
     *
     * @param array|\Traversable $result
     *
     * @return array
     */
    public function splitByLevel($result)
    {
        $highscores = array();
        foreach ($result as $highscore) {
            $highscores[$highscore->getLevel()->getId()][] = $highscore;
        }

        ksort($highscores);
        $highscores = array_reverse($highscores, true);

        return $highscores;
    }
}
