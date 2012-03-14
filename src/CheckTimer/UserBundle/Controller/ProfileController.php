<?php

namespace CheckTimer\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CheckTimer\UserBundle\Entity\User;
use CheckTimer\UserBundle\Form\UserProfileType;

/**
 * @Route("/profile")
 */
class ProfileController extends Controller
{
    /**
     * @Route("/edit", name="profile_edit")
     * @Template
     * @Secure(roles="ROLE_USER")
     */
    public function editAction(Request $request)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new UserProfileType(), $user);

        if ($request->getMethod() == 'POST') {
            $oldUser = clone $user;
            $form->bindRequest($request);

            if ($form->isValid()) {
                if ($user->getPassword() != '') {
                    $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                    $user->setSalt(User::makeSalt());
                    $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
                } else {
                    $user->setPassword($oldUser->getPassword());
                }

                $emailChanged = false;
                if ($user->getNewEmail() != $oldUser->getNewEmail()) {
                    if ($user->getNewEmail() == $user->getEmail() || $user->getNewEmail() == null) {
                        $user->setNewEmail(null);
                        $user->setEmailChangedAt(null);
                    } else {
                        $emailChanged = true;

                        $user->setEmailChangedAt(new \DateTime());
                        $user->setCode(User::makeCode());

                        $message = \Swift_Message::newInstance()
                            ->setSubject('Conferma nuova email')
                            ->setFrom(array($this->container->getParameter('from_email') => $this->container->getParameter('from_name')))
                            ->setTo($user->getNewEmail())
                            ->setBody($this->renderView('CheckTimerUserBundle:Profile:confirmation_email.txt.twig', array(
                                'user' => $user,
                            )))
                        ;
                        $this->get('mailer')->send($message);
                    }
                }

                $this->getDoctrine()->getEntityManager()->flush();

                return $this->render('CheckTimerUserBundle:Profile:edited.html.twig', array(
                    'user'         => $user,
                    'emailChanged' => $emailChanged,
                ));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/email_confirm/{code}", name="profile_emailConfirm", requirements={"code"="[a-zA-Z0-9]+"})
     * @Template("CheckTimerUserBundle:Profile:email_changed.html.twig")
     */
    public function emailConfirmAction(User $user)
    {
        if ($user->getNewEmail() == null || $user->getEmailChangedAt() == null) {
            throw $this->createNotFoundException();
        }

        $user->setEmail($user->getNewEmail());
        $user->setNewEmail(null);
        $user->setEmailChangedAt(null);

        $this->getDoctrine()->getEntityManager()->flush();

        $this->get('session')->setFlash('user/email_changed', 'La tua email è stata cambiata.');

        return array(
            'user' => $user,
        );
    }

    /**
     * @Route("/show/{username}", name="profile_show")
     * @Template
     * @Secure(roles="ROLE_USER")
     */
    public function showAction(User $user)
    {
        if (in_array('ROLE_ADMIN', $user->getRoles()) && !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createNotFoundException();
        }

        $em   = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('CheckTimerGameBundle:Highscore');

        $stats = $repo->getStatistics($user);

        return array(
            'user'  => $user,
            'stats' => $stats,
        );
    }

    /**
     * @Route("/delete", name="profile_delete")
     * @Template
     * @Secure(roles="ROLE_USER")
     */
    public function deleteAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $user = $this->get('security.context')->getToken()->getUser();

            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($user);
            $em->flush();

            $this->get('session')->invalidate();
            $this->get('security.context')->setToken(null);

            $this->get('session')->set('user/profile_deleted', true);

            return $this->redirect($this->generateUrl('profile_deleted'));
        }

        return array();
    }

    /**
     * @Route("/deleted", name="profile_deleted")
     * @Template
     */
    public function deletedAction()
    {
        if (!$this->get('session')->has('user/profile_deleted')) {
            return $this->redirect($this->generateUrl('page_index'));
        }

        $this->get('session')->remove('user/profile_deleted');

        return array();
    }
}
