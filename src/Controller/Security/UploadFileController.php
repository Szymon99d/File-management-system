<?php

namespace App\Controller\Security;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UploadFileController extends AbstractController
{
    #[Route('/upload-file',name:'app_upload_file')]
    public function uploadFile(Request $request, EntityManagerInterface $em): Response
    {
        if($request->isXmlHttpRequest())
        {
            $file = $request->files->get('fileUpload');
            $fileSize = (int)(filesize($file)/1024);
            $fileExtension = $file->getClientOriginalExtension(); 
            $fileName =  str_replace($fileExtension,"",$file->getClientOriginalName());

            $file->move($this->getParameter("file_path"),$fileName.$fileExtension);

            $user = $this->getUser();
            $saveFile = new File;
            $saveFile->setName($fileName);
            $saveFile->setExtension($fileExtension);
            $saveFile->setSize($fileSize);
            $saveFile->setUploadDate(new \DateTime('now'));
            $saveFile->setOwner($user);
            $em->persist($saveFile);
            $em->flush();


            $response = $fileName." ".$fileExtension." ".$fileSize;
            return new JsonResponse($response);
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }
}


?>