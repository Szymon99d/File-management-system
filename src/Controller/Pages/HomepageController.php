<?php

namespace App\Controller\Pages;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class HomepageController extends AbstractController
{
    #[Route('/',name:'app_homepage')]
    public function homepage(Request $request, EntityManagerInterface $em): Response
    {
        $user = new User;
        $userForm = $this->createForm(UserType::class,$user);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid())
        {
            $user = $userForm->getData();
            $em->persist($user);
            $em->flush();
            $this->redirectToRoute('app_homepage');
            //Redirect to user panel TODO later
        }
        return $this->renderForm('pages/homepage.html.twig',[
            'form'=>$userForm,
        ]);
    }

}


?>