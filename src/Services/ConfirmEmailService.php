<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class ConfirmEmailService
{
    private $mailer;
    private $verifyEmail;
    public function __construct(MailerInterface $mailer, VerifyEmailHelperInterface $verifyEmail)
    {
        $this->mailer = $mailer;
        $this->verifyEmail = $verifyEmail;
    }
    public function confirmEmail(User $user): bool
    {
        $signatureComponents = $this->verifyEmail->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail()
        );
        $confirmationEmail = (new Email())
            ->from("admin@fms.local")
            ->to($user->getEmail())
            ->subject("Confirm your email!")
            ->text("To confirm your email address click this link:".$signatureComponents->getSignedUrl()."");
        try {
            $this->mailer->send($confirmationEmail);
            return true;
        } catch (TransportExceptionInterface $e) {
            trigger_error('Mailer Exception '.$e->getMessage());
            return false;
        }

    }



}



?>