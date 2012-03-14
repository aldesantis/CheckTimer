<?php

namespace CheckTimer\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     * @Template
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @Route("/login_check", name="security_loginCheck")
     */
    public function loginCheckAction()
    {
        // This action will be intercepted by the security layer.
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        // This action will be intercepted by the security layer.
    }
}
