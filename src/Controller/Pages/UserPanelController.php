<?php

namespace App\Controller\Pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserPanelController extends AbstractController
{
    #[Route('/user-panel',name:'app_user_panel')]
    public function userPanel(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if(!$user->getIsVerified())
        {
            $this->addFlash('notice','Before you access your file panel, please confirm your email address.');
            return $this->redirectToRoute('app_homepage');
        }
            

        
        return $this->render('pages/user_panel.html.twig',[

        ]);
    }


}

?>