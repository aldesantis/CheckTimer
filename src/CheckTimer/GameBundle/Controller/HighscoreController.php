<?php

namespace CheckTimer\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CheckTimer\GameBundle\Entity\Highscore;
use CheckTimer\UserBundle\Entity\User;

/**
 * @Route("/highscore")
 */
class HighscoreController extends Controller
{
    /**
     * @Route("/", name="highscore_list")
     * @Template
     */
    public function listAction()
    {
        $em   = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('CheckTimerGameBundle:Highscore');

        $highscores = $repo->getLatestOrdered();

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            foreach ($highscores as $k => $h) {
                if (in_array('ROLE_ADMIN', $h->getUser()->getRoles())) {
                    unset($highscores[$k]);
                }
            }
        }

        return array(
            'highscores' => $highscores,
        );
    }

    /**
     * @Route("/{username}", name="highscore_userList")
     * @Template("CheckTimerGameBundle:Highscore:list.html.twig")
     */
    public function userListAction(User $user)
    {
        $em   = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('CheckTimerGameBundle:Highscore');

        $highscores = $repo->orderHighscores($user->getHighscores());

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') && in_array('ROLE_ADMIN', $user->getRoles())) {
            throw $this->createNotFoundException();
        }

        return array(
            'highscores' => $highscores,
        );
    }

    /**
     * @Route("/new", name="highscore_new")
     * @Template
     * @Secure(roles="ROLE_USER")
     */
    public function newAction(Request $request)
    {
        $session = $this->get('session');

        if (!$session->has('highscore/last_level') || !$session->has('highscore/total_time')) {
            return $this->redirect($this->generateUrl('level_play'));
        }

        $em   = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('CheckTimerGameBundle:Level');

        $level = $repo->findOneById($session->get('highscore/last_level'));

        $highscore = new Highscore();

        $highscore->setUser($this->get('security.context')->getToken()->getUser());
        $highscore->setLevel($level);
        $highscore->setTime($session->get('highscore/total_time'));

        $em->persist($highscore);
        $em->flush();

        $session->remove('highscore/last_level');
        $session->remove('highscore/total_time');

        return array(
            'highscore' => $highscore,
        );
    }
}
