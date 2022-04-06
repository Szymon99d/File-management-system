<?php

namespace App\Controller\Security;

use App\Services\UploadFileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UploadFileController extends AbstractController
{
    #[Route('/upload-file',name:'app_upload_file')]
    public function uploadFile(Request $request, UploadFileService $uploadFileService): Response
    {
        if($request->isXmlHttpRequest())
        {
            $file = $request->files->get('fileUpload');
            $fileSize = (int)(filesize($file)/1024);
            $fileExtension = $file->getClientOriginalExtension(); 
            $fileName =  str_replace(".".$fileExtension,"",$file->getClientOriginalName());

            $file->move($this->getParameter("file_path"),$fileName.$fileExtension);

            $user = $this->getUser();
            $uploadFileService->uploadFile($user,$fileName,$fileExtension,$fileSize);

            
            $response = ["fileName"=>$fileName,"fileExtension"=>$fileExtension,"fileSize"=>$fileSize];
            return new JsonResponse($response);
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }
}


?>