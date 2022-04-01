<?php

namespace App\Controller\Pages;

use App\Entity\User;
use App\Form\UserType;
use App\Security\UserAuthenticator;
use App\Services\ConfirmEmailService;
use App\Services\RegisterUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class HomepageController extends AbstractController
{
    #[Route('/',name:'app_homepage')]
    public function homepage(Request $request, 
    RegisterUserService $registerUserService,
    ConfirmEmailService $ces,
    UserAuthenticator $userAuthenticator,
    UserAuthenticatorInterface $userAuthenticatorInterface): Response
    {
        $user = new User;
        $userForm = $this->createForm(UserType::class,$user);
        $userForm->handleRequest($request);
        if($userForm->isSubmitted() && $userForm->isValid())
        {
            $email = $userForm->get('email')->getData();
            $username = $userForm->get('username')->getData();
            $plainpassword = $userForm->get('password')->getData();
            $registerUserService->registerUser($user,$email,$username,$plainpassword);
            $ces->confirmEmail($user);
            $userAuthenticatorInterface->authenticateUser($user,$userAuthenticator,$request);
            $this->redirectToRoute('app_homepage');
        }
        return $this->renderForm('pages/homepage.html.twig',[
            'form'=>$userForm,
        ]);
    }

}


?>