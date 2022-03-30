<?php

namespace App\Services;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ConfirmEmailService
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public function confirmEmail(string $email)
    {
        //Todo
        $confirmationEmail = (new Email())
            ->from("admin@fms.local")
            ->to($email)
            ->subject("Confirm your email!")
            ->text("To confirm your email address click this link:");
        
        try {
            $this->mailer->send($confirmationEmail);
        } catch (TransportExceptionInterface $e) {
            throw $e;
        }

    }



}



?>