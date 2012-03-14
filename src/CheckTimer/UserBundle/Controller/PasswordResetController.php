<?php

namespace CheckTimer\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use CheckTimer\UserBundle\Entity\User;

/**
 * @Route("/password-reset")
 */
class PasswordResetController extends Controller
{
    /**
     * @Route("/", name="passwordReset_request")
     * @Template
     */
    public function requestAction(Request $request)
    {
        $form = $this->createFormBuilder(null, array(
            'validation_constraint' => new Collection(array(
                'id' => new NotBlank(array(
                    'message' => 'Devi inserire uno username o una email.',
                )),
            )),
        ))
            ->add('id', 'text', array(
                'label' => 'Username o email:',
            ))
            ->getForm()
        ;

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em   = $this->getDoctrine()->getEntityManager();
                $repo = $em->getRepository('CheckTimerUserBundle:User');

                $data = $form->getData();
                $user = $repo->findOneByUsernameOrEmail($data['id']);

                if (!$user instanceof User) {
                    $this->get('session')->setFlash('user/not_found', 'L\'utente richiesto non è stato trovato.');

                    return $this->redirect($this->generateUrl('passwordReset_request'));
                }

                if ($user->getPasswordResetCode() != null && $user->getPasswordResetAt() != null) {
                    $this->get('session')->setFlash('user/pending_request', 'Hai una richiesta di reimpostazione in sospeso.');

                    return $this->redirect($this->generateUrl('passwordReset_request'));
                }

                $user->setPasswordResetCode(User::makeCode());
                $user->setPasswordResetAt(new \DateTime());

                $message = \Swift_Message::newInstance()
                    ->setSubject('Reimpostazione password')
                    ->setFrom(array($this->container->getParameter('from_email') => $this->container->getParameter('from_name')))
                    ->setTo($user->getEmail())
                    ->setBody($this->renderView('CheckTimerUserBundle:PasswordReset:confirmation_email.txt.twig', array(
                        'user' => $user,
                    )))
                ;
                $this->get('mailer')->send($message);

                $em->flush();

                return $this->render('CheckTimerUserBundle:PasswordReset:requested.html.twig', array(
                    'user' => $user,
                ));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{password_reset_code}", name="passwordReset_confirm")
     * @Template
     */
    public function confirmAction(User $user, Request $request)
    {
        if ($user->getPasswordResetAt() == null) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder(null, array(
            'validation_constraint' => new Collection(array(
                'newPassword' => array(
                    new NotBlank(array(
                        'message' => 'Devi inserire una password.',
                    )),
                    new MinLength(array(
                        'limit'   => 8,
                        'message' => 'La password deve essere lunga almeno {{ limit }} caratteri.',
                    )),
                    new MaxLength(array(
                        'limit'   => 255,
                        'message' => 'La password deve essere lunga al massimo {{ limit }} caratteri.',
                    ))
                ),
            )),
        ))
            ->add('newPassword', 'repeated', array(
                'type'    => 'password',
                'options' => array(
                    'label' => 'Password:',
                ),
                'invalid_message' => 'Le password non coincidono.',
            ))
            ->getForm()
        ;

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $salt        = User::makeSalt();
                $data        = $form->getData();
                $encoder     = $this->get('security.encoder_factory')->getEncoder($user);
                $newPassword = $encoder->encodePassword($data['newPassword'], $salt);

                $user->setSalt($salt);
                $user->setPassword($newPassword);
                $user->setPasswordResetCode(null);
                $user->setPasswordResetAt(null);

                $this->getDoctrine()->getEntityManager()->flush();

                $this->get('session')->setFlash('user/password_reset', 'La password è stata reimpostata.');

                return $this->redirect($this->generateUrl('security_login'));
            }
        }

        return array(
            'form' => $form->createView(),
            'user' => $user,
        );
    }
}
