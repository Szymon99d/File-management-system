<?php

namespace App\Controller\Pages;

use App\Entity\File;
use App\Services\SelectFilesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserPanelController extends AbstractController
{
    #[Route('/user-panel',name:'app_user_panel')]
    public function userPanel(SelectFilesService $selectFilesService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if(!$user->getIsVerified())
        {
            $this->addFlash('EmailConfWarn','Before you access your file panel, please confirm your email address.');
            return $this->redirectToRoute('app_homepage');
        }
            
        
        
        return $this->render('pages/user_panel.html.twig',[
            'files'=>$selectFilesService->selectAllFiles($user),
        ]);
    }

    #[Route('/select-file/{file}',name:'app_select_file')]
    public function selectFile(Request $request, EntityManagerInterface $em, File $file): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($request->isXmlHttpRequest())
        {
            $fileExtenstion = empty($file->getExtension())?"":".".$file->getExtension();
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileMimeType = finfo_file($finfo,$file->getPath());
            $fileResp = [
            "id"=>$file->getId(),
            "name"=>$file->getName(),
            "extension"=>$fileExtenstion,
            "size"=>$file->getSize(),
            "uploadDate"=>date_format($file->getUploadDate(),"Y-m-d H:i:s"),
            "path"=>$file->getPath(),
            "owner"=>$file->getOwner()->getEmail(),
            "mimeType"=>$fileMimeType
            ];
            if(str_contains($fileMimeType,"text"))
            {
                $fileContents = file_get_contents($file->getPath());
                $fileResp += ["fileContents"=>$fileContents];
            }
            return new JsonResponse(json_encode($fileResp));
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }
    #[Route('/delete-file/{file}',name:'app_delete_file')]
    public function deleteFile(Request $request, EntityManagerInterface $em, Filesystem $filesystem, File $file): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($request->isXmlHttpRequest())
        {
            $filePath = $file->getPath();
            $filesystem->remove($filePath);
            $em->remove($file);
            $em->flush();
            return new JsonResponse();
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }
    #[Route('/rename-file/{file}',name:'app_rename_file')]
    public function renameFile(Request $request, EntityManagerInterface $em, Filesystem $filesystem, File $file): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($request->isXmlHttpRequest())
        {
            $fileName = $request->request->get('name');
            $user = $this->getUser();

            $fileExtenstion = empty($file->getExtension())?"":".".$file->getExtension();

            $filePath = $this->getParameter('file_path').$user->getUsername()."/".$file->getName().$fileExtenstion;
            $file->setName($fileName);
            $fileNewPath = $this->getParameter('file_path').$user->getUsername()."/".$file->getName().$fileExtenstion;
            $file->setPath($fileNewPath);
            $filesystem->rename($filePath,$fileNewPath,true);
            $em->persist($file);
            $em->flush();
            $response = ["fileExtension"=>$fileExtenstion,"filePath"=>$fileNewPath];
            return new JsonResponse(json_encode($response));
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }
    #[Route('/save-changes/{file}',name:'app_save_changes_file')]
    public function saveChangesFile(Request $request, File $file): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $data = $request->request->get('data');
        if($request->isXmlHttpRequest())
        {
            if(file_exists($file->getPath()))
            {
                $fileHandle = fopen($file->getPath(), 'w');
                fwrite($fileHandle, $data);
                fclose($fileHandle);
                
            }
            return new JsonResponse(gettype($data));
        }
        else
            return $this->redirectToRoute('app_user_panel');
    }


}

?>