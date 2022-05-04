<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passHasher;
    public function __construct(UserPasswordHasherInterface $passHasher)
    {
        $this->passHasher = $passHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("TestUser@test.com");
        $user->setUsername("TestUsername");
        $user->setPassword($this->passHasher->hashPassword($user,"TestPassword"));
        $user->setIsVerified(true);
        $manager->persist($user);
        $manager->flush();
    }
}
