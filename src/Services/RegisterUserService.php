<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Exception\ORMException;

class RegisterUserService
{
    private $entityManager;
    private $passwordHasher;
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    public function hashPassword(User $user, string $plainpassword): string
    {
        return $this->passwordHasher->hashPassword($user, $plainpassword);
    }

    public function registerUser(User $user, string $email, string $username, string $password): void
    {
        $this->entityManager->getConnection()->beginTransaction();
        try{
            $user->setEmail($email);
            $user->setUsername($username);
            $user->setPassword($this->hashPassword($user,$password));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        }
        catch(ORMException $e){
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
        
    }

}



?>