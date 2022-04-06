<?php

namespace App\Services;

use App\Entity\File;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class SelectFilesService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function selectAllFiles(User $owner): Collection
    {
        return $owner->getFiles();
    }
}

?>