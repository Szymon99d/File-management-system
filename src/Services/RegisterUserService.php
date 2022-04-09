<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterUserService
{
    private $passwordHasher;
    public function __construct( UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function hashPassword(User $user, string $plainpassword): string
    {
        return $this->passwordHasher->hashPassword($user, $plainpassword);
    }

    public function registerUser(User $user, string $email, string $username, string $password): User
    {
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($this->hashPassword($user,$password));
        return $user;
    }

}



?>