<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UploadFileController extends AbstractController
{
    #[Route('/upload-file',name:'app_upload_file')]
    public function uploadFile(Request $request): Response
    {
        if($request->isXmlHttpRequest())
        {
            //ToDo - upload file
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }
}


?>