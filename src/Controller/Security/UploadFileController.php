<?php

namespace App\Controller\Security;

use App\Services\UploadFileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UploadFileController extends AbstractController
{
    #[Route('/upload-file',name:'app_upload_file')]
    public function uploadFile(Request $request, UploadFileService $uploadFileService, Filesystem $filesystem): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        if($request->isXmlHttpRequest())
        {
            $file = $request->files->get('fileUpload');
            $fileSize = (int)(filesize($file)/1024);
            $fileExtension = $file->getClientOriginalExtension(); 
            $fileName =  str_replace(".".$fileExtension,"",$file->getClientOriginalName());
            $user = $this->getUser();

            $filePath = $this->getParameter('file_path').$user->getUsername()."/".$fileName.".".$fileExtension;
            
            if(!$filesystem->exists($filePath))
            {
                $fileId = $uploadFileService->uploadFile($user,$fileName,$fileExtension,$fileSize,$filePath);
                $file->move($this->getParameter('file_path').$user->getUsername(),$fileName.".".$fileExtension);
    
                $response = ["fileId"=>$fileId,"fileName"=>$fileName,"fileExtension"=>$fileExtension,"fileSize"=>$fileSize];
                return new JsonResponse($response);
            }
            else
            {
                return new JsonResponse(['status'=>false,'message'=>"File already exists!"],403);
            }
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }
}


?>