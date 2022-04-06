<?php

namespace App\Services;

use App\Entity\File;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

class UploadFileService
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function uploadFile(User $owner, string $name, string $extension, int $size): void
    {
        $newFile = new File;
        try{
            $newFile->setOwner($owner);
            $newFile->setName($name);
            $newFile->setExtension($extension);
            $newFile->setSize($size);
            $newFile->setUploadDate(new \DateTime('now'));
            $this->entityManager->persist($newFile);
            $this->entityManager->flush();
        }
        catch(ORMException $e)
        {
            throw $e;
            //ToDo
        }
    }
}


?>