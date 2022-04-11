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

    public function uploadFile(User $owner, string $name, string $extension, int $size, string $path): null|int
    {
        $newFile = new File;
        $this->entityManager->getConnection()->beginTransaction();
        try{
            $newFile->setOwner($owner);
            $newFile->setName($name);
            $newFile->setExtension($extension);
            $newFile->setSize($size);
            $newFile->setPath($path);
            $newFile->setUploadDate(new \DateTime('now'));
            $this->entityManager->persist($newFile);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return $newFile->getId();
        }
        catch(ORMException $e)
        {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
            //ToDo
        }
    }
}


?>