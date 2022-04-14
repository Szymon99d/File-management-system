<?php

namespace App\Controller\Security;

use App\Services\ConfirmEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailController extends AbstractController
{
    #[Route('/verify-email',name: 'app_verify_email')]
    public function verifyEmail(Request $request, VerifyEmailHelperInterface $verifyEmail, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        try {
            $verifyEmail->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $e->getReason());

            return $this->redirectToRoute('app_homepage');
        }
        $user->setIsVerified(true);
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Your e-mail address has been verified!');

        return $this->redirectToRoute('app_homepage');
    }
    #[Route('/resend-confirmation',name:'app_resend_confirmation')]
    public function resendConfirmation(ConfirmEmailService $ces): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if($user->getIsVerified())
            return $this->redirectToRoute('app_user_panel');
        $ces->confirmEmail($user);
        $this->addFlash('success',"Email confirmation has been successfully sent!");
        return $this->redirectToRoute('app_homepage');
    }
}


?>