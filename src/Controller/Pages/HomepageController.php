<?php

namespace App\Controller\Pages;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class HomepageController extends AbstractController
{
    #[Route('/',name:'app_homepage')]
    public function homepage(Request $request): Response
    {
        $user = new User;
        $userForm = $this->createForm(UserType::class,$user);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid())
        {
            $user = $userForm->getData();
            $this->redirectToRoute('app_homepage');
        }
        return $this->renderForm('pages/homepage.html.twig',[
            'form'=>$userForm,
        ]);
    }

}


?>