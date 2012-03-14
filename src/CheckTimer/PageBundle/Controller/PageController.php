<?php

namespace CheckTimer\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PageController extends Controller
{
    /**
     * @Route("/", name="page_index")
     */
    public function indexAction()
    {
        return $this->forward('CheckTimerPageBundle:Page:view', array(
            'slug' => 'home',
        ));
    }

    /**
     * @Route("/{slug}", name="page_view", requirements={"slug"="home|rules|contacts"}, defaults={"slug"="home"})
     */
    public function viewAction($slug)
    {
        return $this->render(sprintf('CheckTimerPageBundle:Page:%s.html.twig', $slug));
    }
}
