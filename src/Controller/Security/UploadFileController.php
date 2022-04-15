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
            $file = $request->files->get('files');
            $fileCount = count($file);
            $responseFiles = array();
            for($i=0; $i<$fileCount; $i++)
            {
                $fileSize = (int)(filesize($file[$i])/1024);
                $fileExtension = $file[$i]->getClientOriginalExtension(); 
                $fileName =  str_replace(".".$fileExtension,"",$file[$i]->getClientOriginalName());
                $fileFullName = $file[$i]->getClientOriginalName();
                $user = $this->getUser();
    
                $filePath = $this->getParameter('file_path').$user->getUsername()."/".$fileFullName;
                if(!$filesystem->exists($filePath))
                {
                    $fileId = $uploadFileService->uploadFile($user,$fileName,$fileExtension,$fileSize,$filePath);
                    $file[$i]->move($this->getParameter('file_path').$user->getUsername(),$fileFullName);
        
                    $response = ["fileId"=>$fileId,"fileName"=>$fileName,"fileExtension"=>$fileExtension,"fileSize"=>$fileSize];
                    array_push($responseFiles,$response);
                }
            }
            return new JsonResponse(json_encode($responseFiles));
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }
}


?>