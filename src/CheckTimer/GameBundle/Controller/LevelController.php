<?php

namespace CheckTimer\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CheckTimer\GameBundle\Entity\Level;

class LevelController extends Controller
{
    /**
     * @Route("/", name="level_play")
     * @Template
     * @Secure(roles="ROLE_USER")
     */
    public function playAction(Request $request)
    {
        $session = $this->get('session');

        $em   = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('CheckTimerGameBundle:Level');

        $firstLevel = $repo->getFirst();

        // The user is currently playing the game.
        if ($session->has('game/last_level')) {
            $level = $repo->findOneById($session->get('game/last_level') + 1);
        } else {
            $level = $firstLevel;
        }

        // The level does not exist (no levels created?).
        if (!$level instanceof Level) {
            throw $this->createNotFoundException();
        }

        if ($request->getMethod() == 'POST') {
            // The user hit the STOP button.
            if ($session->has('level/start_time')) {
                $time = microtime(true) - $session->get('level/start_time');

                // The user won the level.
                if ($time >= $level->getMinTime() && $time <= $level->getMaxTime()) {
                    $session->set('game/last_level', $level->getId());
                    $lastLevel = $repo->getLast();

                    // This is the last level, so create the highscore.
                    if ($lastLevel->getId() == $level->getId()) {
                        return $this->redirectToHighscore();
                    }

                    // Not the last level, redirect to the next one.
                    $session->remove('level/start_time');

                    return $this->redirect($this->generateUrl('level_play'));
                }

                // The user didn't win the level, create the highscore.
                if ($level->getId() > $firstLevel->getId()) {
                    return $this->redirectToHighscore();
                }

                // The user couldn't pass the first level. What a loser!
                $this->deleteData();
                $session->setFlash('game/game_lost', 'Hai perso al primo livello. Non sei molto bravo, eh?');

                return $this->redirect($this->generateUrl('level_play'));
            }

            $session->set('level/start_time', microtime(true));

            // If this is the first level, start the internal timer as well.
            if ($level->getId() == $firstLevel->getId()) {
                $session->set('game/start_time', round(microtime(true), 2));
            }
        }

        return array(
            'level' => $level,
        );
    }

    /**
     * Sets the session values and redirects to the highscore creation page.
     *
     * @return RedirectResponse
     */
    protected function redirectToHighscore()
    {
        $session = $this->get('session');

        $session->set('highscore/last_level', $session->get('game/last_level'));
        $session->set('highscore/total_time', round(microtime(true) - $session->get('game/start_time'), 2));

        $this->deleteData();

        return $this->redirect($this->generateUrl('highscore_new'));
    }

    /**
     * Deletes all the game data.
     */
    protected function deleteData()
    {
        $session = $this->get('session');

        $session->remove('game/last_level');
        $session->remove('game/start_time');
        $session->remove('level/start_time');
    }
}
