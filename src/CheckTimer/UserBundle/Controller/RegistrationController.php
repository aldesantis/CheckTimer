<?php

namespace CheckTimer\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CheckTimer\UserBundle\Entity\User;
use CheckTimer\UserBundle\Form\UserRegistrationType;

/**
 * @Route("/register")
 */
class RegistrationController extends Controller
{
    /**
     * @Route("/", name="registration_register")
     * @Template
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserRegistrationType(), $user);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);

                $message = \Swift_Message::newInstance()
                    ->setSubject('Conferma registrazione')
                    ->setFrom(array($this->container->getParameter('from_email') => $this->container->getParameter('from_name')))
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView('CheckTimerUserBundle:Registration:confirmation_email.txt.twig', array(
                        'user' => $user,
                    )))
                ;
                $this->get('mailer')->send($message);

                $em->flush();

                return $this->render('CheckTimerUserBundle:Registration:registered.html.twig', array(
                    'user' => $user,
                ));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/confirm/{code}", name="registration_confirm", requirements={"code"="[a-zA-Z0-9]+"})
     */
    public function confirmAction(User $user)
    {
        if ($user->getEnabled()) {
            $message = 'Il tuo account è già stato confermato.';
        } else {
            $user->setEnabled(true);
            $this->getDoctrine()->getEntityManager()->flush();
            $message = 'Il tuo account è stato confermato con successo!';
        }

        $this->get('session')->setFlash('user/account_confirmed', $message);

        return $this->redirect($this->generateUrl('security_login'));
    }
}
